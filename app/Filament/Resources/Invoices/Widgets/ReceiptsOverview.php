<?php

namespace App\Filament\Resources\Receipts\Widgets;

use App\Models\Receipt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReceiptsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Receipts', Receipt::count()),
            Stat::make('Total Amount', Receipt::sum('total_amount')),
            Stat::make('Pending Receipts', Receipt::where('status', 'pending')->count()),
            Stat::make('Paid Receipts', Receipt::where('status', 'paid')->count()),
            Stat::make('Overdue Receipts', Receipt::where('due_date', '<', now())->count()),
            Stat::make('Draft Receipts', Receipt::where('status', 'draft')->count()),
        ];
    }
}
