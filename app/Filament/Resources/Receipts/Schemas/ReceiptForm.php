<?php

namespace App\Filament\Resources\Receipts\Schemas;

use Filament\Schemas\Schema;
use OpenAI\Laravel\Facades\OpenAI;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ReceiptForm
{
          public static function configure(Schema $schema): Schema
          {
              return $schema
                  ->components([
                      FileUpload::make('original_file_path')
                          ->label('Upload Receipt')
                          ->directory('receipts')
                          ->required()
                          ->afterStateUpdated(function (Set $set, Get $get, $state) {
                              if (! $state) {
                                  return;
                              }
                              if ($state instanceof \Illuminate\Http\UploadedFile) {
                                  $base64 = base64_encode(file_get_contents($state->getRealPath()));
      
                                  $data = self::extractReceiptData($base64, $state->getMimeType());
                                  $data = preg_replace('/^```json\s*(.*)\s*```$/s', '$1', $data);
                                  $data = json_decode($data);
                                  $set('vendor_name', $data->vendor_name ?? '');
                                  $set('vendor_email', $data->vendor_email ?? '');
                                  $set('vendor_phone', $data->vendor_phone ?? '');
                                  $set('vendor_address', $data->vendor_address ?? '');
                                  $set('bank_account', $data->bank_account ?? '');
                                  $set('account_number', $data->account_number ?? '');
                                  $set('receipt_number', $data->receipt_number ?? '');
                                  $set('po_number', $data->po_number ?? '');
                                  $set('receipt_date', $data->receipt_date ?? '');
                                  $set('due_date', $data->due_date ?? '');
                                  $set('total_amount', $data->total_amount ?? '');
                                  $set('tax_amount', $data->tax_amount ?? '');
                                  $set('currency', $data->currency ?? '');
                                  $set('term_conditions', $data->term_conditions ?? '');
                              }
                          })
                          ->image()
                          ->imagePreviewHeight('860')
                          ->openable()
                          ->maxSize(1024 * 5) // 5 MB
                          ->acceptedFileTypes(['image/*']),
      
                      Section::make([
                          Fieldset::make('Receipt Information')
                              ->schema([
                                  TextInput::make('receipt_number'),
                                  TextInput::make('po_number'),
                                  TextInput::make('total_amount')
                                      ->numeric(),
                                  DatePicker::make('receipt_date'),
                                  DatePicker::make('due_date'),
                                  TextInput::make('tax_amount')
                                      ->numeric(),
                                  TextInput::make('currency'),
                              ]),
                          Fieldset::make('Vendor Information')
                              ->schema([
                                  TextInput::make('vendor_name')
                                      ->label('Vendor Name'),
                                  TextInput::make('vendor_email')
                                      ->label('Vendor Email')
                                      ->email(),
                                  TextInput::make('vendor_phone')
                                      ->label('Vendor Phone')
                                      ->tel(),
                                  TextInput::make('vendor_address')
                                      ->label('Vendor Address'),
                                  TextInput::make('bank_account')
                                      ->label('Bank Account'),
                                  TextInput::make('account_number')
                                      ->label('Account Number'),
                              ]),
      
      
                              Fieldset::make('Additional Information')
                              ->schema([
                                  Textarea::make('term_conditions')
                                      ->columnSpanFull(),
                                  Textarea::make('notes')
                                      ->columnSpanFull(),
                              ]),
                      ]),
                  ]);
          }
      
          public static function extractReceiptData($base64, $fileType)
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
                                  'text' => "Extract structured data from this receipt file as JSON:\n\nReturn format:\n".
                              json_encode([
                                  'vendor_name' => 'ABC Supplies',
                                  'vendor_email' => 'contact@abcsupplies.com',
                                  'vendor_phone' => '123-456-7890',
                                  'vendor_address' => '123 Supply St, Jakarta',
                                  'bank_account' => 'Bank Mandiri',
                                  'account_number' => '1234567890',
                                  'receipt_number' => 'INV-001',
                                  'po_number' => 'PO-001',
                                  'receipt_date' => '2025-06-27',
                                  'due_date' => '2025-07-27',
                                  'total_amount' => '1234.56',
                                  'tax_amount' => '123.45',
                                  'currency' => 'Rp',
                                  'term_conditions' => 'TERMS & CONDITIONS \n 
                                  Payment is due within 30 days',
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
