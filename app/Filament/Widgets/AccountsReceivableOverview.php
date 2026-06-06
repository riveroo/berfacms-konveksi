<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountsReceivableOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $totalTransactions = Transaction::where('status', '!=', 'cancelled')
            ->sum('grand_total');

        $totalPayments = TransactionPayment::whereHas('transaction', function ($q) {
            $q->where('status', '!=', 'cancelled');
        })->sum('amount');

        $outstandingReceivable = $totalTransactions - $totalPayments;

        return [
            Stat::make(__('accounts_receivable.overview_total_transactions'), 'Rp ' . number_format($totalTransactions, 0, ',', '.'))
                ->description(__('accounts_receivable.overview_total_transactions_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make(__('accounts_receivable.overview_total_payments'), 'Rp ' . number_format($totalPayments, 0, ',', '.'))
                ->description(__('accounts_receivable.overview_total_payments_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('accounts_receivable.overview_outstanding_receivables'), 'Rp ' . number_format($outstandingReceivable, 0, ',', '.'))
                ->description(__('accounts_receivable.overview_outstanding_receivables_desc'))
                ->descriptionIcon('heroicon-m-scale')
                ->color($outstandingReceivable > 0 ? 'danger' : 'success'),
        ];
    }
}
