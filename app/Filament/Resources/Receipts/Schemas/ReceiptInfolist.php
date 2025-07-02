<?php

namespace App\Filament\Resources\Receipts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class ReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
          Section::make([
                    ImageEntry::make('original_file_path')
                              ->label('Receipt')
                              ->height(800)
                              ->alignCenter(),
          ]),

                
          Section::make([
                    Fieldset::make('Receipt Information')
                        ->schema([
                            TextEntry::make('receipt_number'),
                            TextEntry::make('po_number'),
                            TextEntry::make('total_amount')
                                ->numeric(),
                            TextEntry::make('receipt_date')->date(),
                            TextEntry::make('due_date')->date(),
                            TextEntry::make('tax_amount')
                                ->numeric(),
                            TextEntry::make('currency'),
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
                            TextEntry::make('term_conditions')
                                ->columnSpanFull(),
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
