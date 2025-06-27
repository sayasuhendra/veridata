<?php

namespace App\Filament\Resources\Receipts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('number'),
                TextEntry::make('date'),
                TextEntry::make('name'),
                TextEntry::make('total'),
                IconEntry::make('verified')
                    ->boolean(),
            ]);
    }
}
