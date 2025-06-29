<?php

namespace App\Filament\Resources\Invoices\Schemas;

set_time_limit(6000);
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('original_file_path')
                    ->label('Upload Invoice')
                    ->disk('public')
                    ->directory('invoices')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (! $state) {
                            return;
                        }
                        $file = $state->getRealPath();
                        // if (substr($file, -3) == 'pdf') {
                        //     $pdf = new \Spatie\PdfToImage\Pdf($file);
                        //     $image = $pdf->save(storage_path('app/public/invoices'));
                        //     dd($image);
                        // }
                        $base64 = base64_encode(file_get_contents($file));

                        $data = self::extractInvoiceData($base64, $state->getMimeType());
                        dd($data);
                        // $this->invoice->update([
                        //     'raw_text' => $ocrText,
                        //     'invoice_number' => $data['invoice_number'] ?? null,
                        //     'invoice_date' => $data['invoice_date'] ?? null,
                        //     'vendor_name' => $data['vendor_name'] ?? null,
                        //     'total_amount' => $data['total_amount'] ?? null,
                        //     'line_items' => json_encode($data['line_items'] ?? []),
                        // ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil')
                            ->body($data)
                            ->success()
                            ->send();
                        // Dapatkan path file yang baru diupload
                        // $filePath = Storage::disk('public')->path($state->getRealPath());
                        // $mimeType = $state->getMimeType();
                        //
                        // // Panggil service AI kita
                        // $aiService = new DocumentAIService;
                        // $extractedData = $aiService->processInvoice($filePath, $mimeType);
                        //
                        // if ($extractedData) {
                        //     // Isi form lain dengan data dari AI
                        //     $set('vendor_name', $extractedData['supplier_name'] ?? null);
                        //     $set('invoice_id', $extractedData['invoice_id'] ?? null);
                        //     $set('invoice_date', $extractedData['invoice_date'] ?? null);
                        //     $set('due_date', $extractedData['due_date'] ?? null);
                        //
                        //     // Untuk angka, bersihkan dulu dari simbol mata uang atau koma
                        //     $total = preg_replace('/[^0-9.]/', '', $extractedData['total_amount'] ?? '0');
                        //     $tax = preg_replace('/[^0-9.]/', '', $extractedData['total_tax_amount'] ?? '0');
                        //
                        //     $set('total_amount', $total);
                        //     $set('tax_amount', $tax);
                        //     $set('currency', $extractedData['currency'] ?? null);
                        //
                        //     // Simpan response mentah jika perlu
                        //     $set('raw_ai_response', $extractedData['raw_response']);
                        // } else {
                        //     // Beri notifikasi jika gagal
                        //     \Filament\Notifications\Notification::make()
                        //         ->title('Processing Failed')
                        //         ->body('Could not extract data from the document.')
                        //         ->danger()
                        //         ->send();
                        // }
                    }),

                Fieldset::make('Extracted Information')
                    ->schema([
                        TextInput::make('vendor_name'),
                        TextInput::make('invoice_id'),
                        DatePicker::make('invoice_date'),
                        DatePicker::make('due_date'),
                        TextInput::make('total_amount')->numeric()->prefix('$'),
                        TextInput::make('tax_amount')->numeric()->prefix('$'),
                        TextInput::make('currency')->maxLength(10),
                    ]),
            ]);
    }

    public static function extractInvoiceData($base64, $fileType)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:$fileType;base64,$base64",
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => "Extract structured data from this invoice file as JSON:\n\nReturn format:\n".
                        json_encode([
                            'invoice_number' => 'INV-001',
                            'invoice_date' => '2024-06-01',
                            'vendor_name' => 'ABC Supplies',
                            'total_amount' => '1234.56',
                            'line_items' => [
                                ['item' => 'Widget A', 'qty' => 2, 'price' => 500],
                                ['item' => 'Widget B', 'qty' => 1, 'price' => 234.56],
                            ],
                        ], JSON_PRETTY_PRINT),
                        ],
                    ],
                ],
            ],
        ]);

        return json_decode($response->choices[0]->message->content, true);
    }
}
