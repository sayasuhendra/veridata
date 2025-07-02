<?php

namespace App\Filament\Resources\Invoices\Widgets;

use App\Models\Invoice;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoicesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
                    Stat::make('Total Invoices', Invoice::count()),
                    Stat::make('Total Amount', Invoice::sum('total_amount')),
                    Stat::make('Pending Invoices', Invoice::where('status', 'pending')->count()),
                    Stat::make('Paid Invoices', Invoice::where('status', 'paid')->count()),
                    Stat::make('Overdue Invoices', Invoice::where('due_date', '<', now())->count()),
                    Stat::make('Draft Invoices', Invoice::where('status', 'draft')->count()),
        ];
    }
}
