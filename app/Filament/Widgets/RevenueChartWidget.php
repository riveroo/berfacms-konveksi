<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Revenue Trend (Last 30 Days)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 3;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = (float) Transaction::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('grand_total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (IDR)',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
