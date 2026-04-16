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

    protected function getStats(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Total Revenue (Paid, On Progress, Done) in current month
        $revenue = Transaction::whereIn('status', ['paid', 'on progress', 'done'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('grand_total');

        // Total Orders this month
        $totalOrders = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Pending Orders (Pre Orders) status: on process, accepted
        $pendingPreOrders = PreOrder::whereIn('status', ['on process', 'accepted'])->count();

        // Pending Payments (Transaction status: waiting for payment)
        $pendingPayments = Transaction::where('status', 'waiting for payment')->count();

        // Orders in Production (Transaction status: on progress)
        $ordersInProduction = Transaction::where('status', 'on progress')->count();

        return [
            Stat::make('Total Revenue (Bulan Ini)', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description('Total pendapatan yang valid')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Order (Bulan Ini)', $totalOrders)
                ->description('Jumlah transaksi terdaftar')
                ->descriptionIcon('heroicon-m-shopping-cart'),

            Stat::make('Pending Orders (PO)', $pendingPreOrders)
                ->description('PO dalam proses / diterima')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Menunggu Pembayaran', $pendingPayments)
                ->description('Transaksi belum dibayar')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('danger'),

            Stat::make('Dalam Produksi', $ordersInProduction)
                ->description('Pesanan sedang dikerjakan')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('primary'),
        ];
    }
}
