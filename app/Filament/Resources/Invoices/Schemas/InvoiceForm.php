<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Services\DocumentAIService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('original_file_path')
                    ->label('Upload Invoice/Receipt')
                    ->disk('public') // Pastikan 'public' disk dikonfigurasi
                    ->directory('invoices')
                    ->required()
                    ->live() // PENTING: Mengaktifkan Livewire
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (! $state) {
                            return;
                        }

                        // Dapatkan path file yang baru diupload
                        $filePath = Storage::disk('public')->path($state->getRealPath());
                        $mimeType = $state->getMimeType();

                        // Panggil service AI kita
                        $aiService = new DocumentAIService;
                        $extractedData = $aiService->processInvoice($filePath, $mimeType);

                        if ($extractedData) {
                            // Isi form lain dengan data dari AI
                            $set('vendor_name', $extractedData['supplier_name'] ?? null);
                            $set('invoice_id', $extractedData['invoice_id'] ?? null);
                            $set('invoice_date', $extractedData['invoice_date'] ?? null);
                            $set('due_date', $extractedData['due_date'] ?? null);

                            // Untuk angka, bersihkan dulu dari simbol mata uang atau koma
                            $total = preg_replace('/[^0-9.]/', '', $extractedData['total_amount'] ?? '0');
                            $tax = preg_replace('/[^0-9.]/', '', $extractedData['total_tax_amount'] ?? '0');

                            $set('total_amount', $total);
                            $set('tax_amount', $tax);
                            $set('currency', $extractedData['currency'] ?? null);

                            // Simpan response mentah jika perlu
                            $set('raw_ai_response', $extractedData['raw_response']);
                        } else {
                            // Beri notifikasi jika gagal
                            \Filament\Notifications\Notification::make()
                                ->title('Processing Failed')
                                ->body('Could not extract data from the document.')
                                ->danger()
                                ->send();
                        }
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
}
