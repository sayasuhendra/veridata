<?php

namespace App\Filament\Resources\Receipts\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ReceiptExporter;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\Receipts\ReceiptResource;

class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
                ->exporter(ReceiptExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ]),
        ];
    }
}
