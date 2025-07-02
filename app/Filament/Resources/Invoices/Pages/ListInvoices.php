<?php

namespace App\Filament\Resources\Invoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\InvoiceExporter;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\Invoices\InvoiceResource;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
                ->exporter(InvoiceExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ]),
        ];
    }
}
