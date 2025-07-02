<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
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
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (! $state) {
                            return;
                        }
                        if ($state instanceof \Illuminate\Http\UploadedFile) {
                            $base64 = base64_encode(file_get_contents($state->getRealPath()));

                            $data = self::extractInvoiceData($base64, $state->getMimeType());
                            $data = preg_replace('/^```json\s*(.*)\s*```$/s', '$1', $data);
                            $data = json_decode($data, true);
                            $set('vendor_name', $data['vendor_name'] ?? '');
                            $set('invoice_id', $data['invoice_number'] ?? '');
                            $set('invoice_date', $data['invoice_date'] ?? '');
                            $set('due_date', $data['due_date'] ?? '');
                            $set('total_amount', $data['total_amount'] ?? '');
                            $set('tax_amount', $data['tax_amount'] ?? '');
                            $set('currency', $data['currency'] ?? '');
                        }
                    })
                    ->image()
                    ->imagePreviewHeight('600')
                    ->openable()
                    ->maxSize(1024 * 5) // 5 MB
                    ->acceptedFileTypes(['image/*', 'application/pdf']),

                Fieldset::make('Extracted Information')
                    ->schema([
                        TextInput::make('vendor_name'),
                        TextInput::make('invoice_id'),
                        DatePicker::make('invoice_date'),
                        DatePicker::make('due_date'),
                        TextInput::make('total_amount')->numeric()->prefix('Rp'),
                        TextInput::make('tax_amount')->numeric()->prefix('Rp'),
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

        return $response->choices[0]->message->content;
    }
}
