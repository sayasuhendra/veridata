<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('vendor_name'),
                TextEntry::make('invoice_id'),
                TextEntry::make('invoice_date')
                    ->date(),
                TextEntry::make('due_date')
                    ->date(),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('currency'),
                TextEntry::make('original_file_path'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
