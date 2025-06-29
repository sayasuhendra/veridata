<?php

namespace App\Filament\Resources\Receipts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vendor_name'),
                TextInput::make('invoice_id'),
                DatePicker::make('invoice_date'),
                DatePicker::make('due_date'),
                TextInput::make('total_amount')
                    ->numeric(),
                TextInput::make('tax_amount')
                    ->numeric(),
                TextInput::make('currency'),
                TextInput::make('original_file_path')
                    ->required(),
                Textarea::make('line_items')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                Textarea::make('ocr_text')
                    ->columnSpanFull(),
                Textarea::make('raw_ai_response')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
