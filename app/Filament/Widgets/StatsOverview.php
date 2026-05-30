<?php

namespace App\Filament\Widgets;

use App\Models\PreOrder;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 5;
    }

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Total Orders This Month
        $totalOrders = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // 2. Total POs This Month
        $totalPOs = PreOrder::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // 3. Total Revenue This Month
        $revenue = Transaction::where('status', '!=', 'cancelled')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('grand_total');

        // 4. Outstanding Receivables
        $totalTransactions = Transaction::where('status', '!=', 'cancelled')
            ->sum('grand_total');

        $allPayments = \App\Models\TransactionPayment::whereHas('transaction', function ($q) {
            $q->where('status', '!=', 'cancelled');
        })->sum('amount');

        $outstandingReceivable = $totalTransactions - $allPayments;

        // 5. Total Payments
        $currentMonthPayments = \App\Models\TransactionPayment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('amount');

        $currentMonthYear = now()->translatedFormat('F Y');

        return [
            Stat::make('Total Orders', $totalOrders)
                ->description($currentMonthYear)
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('indigo'),

            Stat::make('Total POs', $totalPOs)
                ->description($currentMonthYear)
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),

            Stat::make('Total Revenue', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description($currentMonthYear)
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Outstanding', 'Rp ' . number_format($outstandingReceivable, 0, ',', '.'))
                ->description('Remaining unpaid')
                ->descriptionIcon('heroicon-m-scale')
                ->color($outstandingReceivable > 0 ? 'danger' : 'success'),

            Stat::make('Total Payments', 'Rp ' . number_format($currentMonthPayments, 0, ',', '.'))
                ->description($currentMonthYear)
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
