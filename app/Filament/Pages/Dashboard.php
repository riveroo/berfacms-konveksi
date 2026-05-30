<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 4;
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\OrderChartWidget::class,
            \App\Filament\Widgets\TopProductsWidget::class,
            \App\Filament\Widgets\RevenueChartWidget::class,
            \App\Filament\Widgets\TopTransactionsWidget::class,
        ];
    }
}
