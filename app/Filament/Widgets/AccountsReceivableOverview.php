<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountsReceivableOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTransactions = Transaction::where('status', '!=', 'cancelled')
            ->sum('grand_total');

        $totalPayments = TransactionPayment::whereHas('transaction', function ($q) {
            $q->where('status', '!=', 'cancelled');
        })->sum('amount');

        $outstandingReceivable = $totalTransactions - $totalPayments;

        return [
            Stat::make('Total Transactions', 'Rp ' . number_format($totalTransactions, 0, ',', '.'))
                ->description('Sum of all customer transaction amounts')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Total Payments', 'Rp ' . number_format($totalPayments, 0, ',', '.'))
                ->description('Sum of all payments received')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Outstanding Receivables', 'Rp ' . number_format($outstandingReceivable, 0, ',', '.'))
                ->description('Total remaining outstanding balance')
                ->descriptionIcon('heroicon-m-scale')
                ->color($outstandingReceivable > 0 ? 'danger' : 'success'),
        ];
    }
}
