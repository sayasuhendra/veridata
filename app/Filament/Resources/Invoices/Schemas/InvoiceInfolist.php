<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
          return $schema
          ->components([
                              ImageEntry::make('original_file_path')
                              ->label('Invoice')
                              ->alignCenter()
                              ->imageWidth('900')
                              ->imageHeight('1200'),
                          
                    Section::make([
                              Fieldset::make('Invoice Information')
                                  ->schema([
                                      TextEntry::make('invoice_number'),
                                      TextEntry::make('total_amount')
                                          ->numeric(),
                                      TextEntry::make('invoice_date')->date(),
                                      TextEntry::make('due_date')->date(),
                                      TextEntry::make('tax_amount')
                                          ->numeric(),
                                      TextEntry::make('currency'),
                                      TextEntry::make('status')
                                          ->default('pending'),
                                  ]),
                              Fieldset::make('Vendor Information')
                                  ->schema([
                                      TextEntry::make('vendor_name')
                                          ->label('Vendor Name'),
                                      TextEntry::make('vendor_email')
                                          ->label('Vendor Email'),
                                      TextEntry::make('vendor_phone')
                                          ->label('Vendor Phone'),
                                      TextEntry::make('vendor_address')
                                          ->label('Vendor Address'),
                                      TextEntry::make('bank_account')
                                          ->label('Bank Account'),
                                      TextEntry::make('account_number')
                                          ->label('Account Number'),
                                  ]),
          
          
                                  Fieldset::make('Additional Information')
                                  ->schema([
                                      TextEntry::make('notes')
                                          ->columnSpanFull(),
                                          TextEntry::make('created_at')
                                          ->dateTime(),
                                      TextEntry::make('updated_at')
                                          ->dateTime(),
                                  ]),
          
                          ]),
          
                      ]);
    }
}
