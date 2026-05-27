<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProfitLossExport implements FromArray, WithTitle
{
    protected $revenueAccounts;
    protected $expenseAccounts;
    protected $totalRevenue;
    protected $totalExpense;
    protected $netProfit;
    protected $profitMargin;
    protected $periodLabel;

    public function __construct(
        $revenueAccounts,
        $expenseAccounts,
        $totalRevenue,
        $totalExpense,
        $netProfit,
        $profitMargin,
        $periodLabel
    ) {
        $this->revenueAccounts = $revenueAccounts;
        $this->expenseAccounts = $expenseAccounts;
        $this->totalRevenue = $totalRevenue;
        $this->totalExpense = $totalExpense;
        $this->netProfit = $netProfit;
        $this->profitMargin = $profitMargin;
        $this->periodLabel = $periodLabel;
    }

    public function array(): array
    {
        $rows = [
            ['PROFIT & LOSS REPORT'],
            ['Period: ' . $this->periodLabel],
            [],
            ['SUMMARY METADATA'],
            ['Total Income', 'Rp ' . number_format($this->totalRevenue, 0, ',', '.')],
            ['Total Expenses', 'Rp ' . number_format($this->totalExpense, 0, ',', '.')],
            ['Net Profit', 'Rp ' . number_format($this->netProfit, 0, ',', '.')],
            ['Profit Margin', number_format($this->profitMargin, 2, ',', '.') . '%'],
            [],
            ['ACCOUNT DETAILED STATEMENT'],
            ['Account Code', 'Account Name', 'Balance'],
            [],
            ['INCOME'],
        ];

        // Add revenue accounts
        foreach ($this->revenueAccounts as $account) {
            $rows[] = [
                $account->code,
                $account->name,
                'Rp ' . number_format($account->balance, 0, ',', '.')
            ];
        }

        $rows[] = ['SUBTOTAL INCOME', '', 'Rp ' . number_format($this->totalRevenue, 0, ',', '.')];
        $rows[] = [];
        $rows[] = ['EXPENSES'];

        // Add expense accounts
        foreach ($this->expenseAccounts as $account) {
            $rows[] = [
                $account->code,
                $account->name,
                'Rp ' . number_format($account->balance, 0, ',', '.')
            ];
        }

        $rows[] = ['SUBTOTAL EXPENSES', '', 'Rp ' . number_format($this->totalExpense, 0, ',', '.')];
        $rows[] = [];
        $rows[] = ['NET PROFIT / LOSS', '', 'Rp ' . number_format($this->netProfit, 0, ',', '.')];

        return $rows;
    }

    public function title(): string
    {
        return 'Profit & Loss Statement';
    }
}
