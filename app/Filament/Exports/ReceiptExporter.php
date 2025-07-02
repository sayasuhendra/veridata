<?php

namespace App\Filament\Exports;

use App\Models\Receipt;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ReceiptExporter extends Exporter
{
    protected static ?string $model = Receipt::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('vendor_name'),
            ExportColumn::make('vendor_email'),
            ExportColumn::make('vendor_phone'),
            ExportColumn::make('vendor_address'),
            ExportColumn::make('bank_account'),
            ExportColumn::make('account_number'),
            ExportColumn::make('po_number'),
            ExportColumn::make('receipt_number'),
            ExportColumn::make('receipt_date'),
            ExportColumn::make('due_date'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('tax_amount'),
            ExportColumn::make('currency'),
            ExportColumn::make('original_file_path'),
            ExportColumn::make('line_items'),
            ExportColumn::make('raw_ai_response'),
            ExportColumn::make('term_conditions'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your receipt export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
