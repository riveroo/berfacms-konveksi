<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class BalanceSheetExport implements FromArray, WithTitle
{
    protected array $data;
    protected string $period;

    public function __construct(array $data, string $period)
    {
        $this->data = $data;
        $this->period = $period;
    }

    public function array(): array
    {
        $rows = [
            ['BALANCE SHEET REPORT'],
            ['Period: ' . $this->data['period_label']],
            ['Validation Status: ' . ($this->data['isBalanced'] ? 'Balanced' : 'Out of Balance (Diff: Rp ' . number_format($this->data['difference'], 0, ',', '.') . ')')],
            [],
            ['Category / Account', 'Code', 'Balance'],
            [],
            ['ASSETS'],
            ['Current Assets'],
        ];

        // 1. Cash & Equivalents
        $rows[] = ['  Cash and Cash Equivalents'];
        foreach ($this->data['cashAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Cash and Cash Equivalents', '', 'Rp ' . number_format($this->data['totalCash'], 0, ',', '.')];
        
        // 2. AR
        $rows[] = ['  Accounts Receivable'];
        foreach ($this->data['arAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Accounts Receivable', '', 'Rp ' . number_format($this->data['totalAR'], 0, ',', '.')];

        // 3. Inventory
        $rows[] = ['  Inventory'];
        foreach ($this->data['inventoryAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Inventory', '', 'Rp ' . number_format($this->data['totalInventory'], 0, ',', '.')];
        
        $rows[] = ['TOTAL CURRENT ASSETS', '', 'Rp ' . number_format($this->data['totalCurrentAssets'], 0, ',', '.')];
        $rows[] = [];

        // Non-Current Assets
        $rows[] = ['Non-Current Assets'];
        $rows[] = ['  Property, Plant and Equipment'];
        foreach ($this->data['ppeAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Property, Plant and Equipment', '', 'Rp ' . number_format($this->data['totalPPE'], 0, ',', '.')];

        $rows[] = ['  Less: Accumulated Depreciation'];
        foreach ($this->data['depreciationAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Accumulated Depreciation', '', 'Rp ' . number_format($this->data['totalDepreciation'], 0, ',', '.')];
        
        $rows[] = ['TOTAL NON-CURRENT ASSETS', '', 'Rp ' . number_format($this->data['totalNonCurrentAssets'], 0, ',', '.')];
        $rows[] = [];
        $rows[] = ['TOTAL ASSETS', '', 'Rp ' . number_format($this->data['totalAssets'], 0, ',', '.')];
        $rows[] = [];
        
        // LIABILITIES & EQUITY
        $rows[] = ['LIABILITIES & EQUITY'];
        $rows[] = ['Current Liabilities'];
        $rows[] = ['  Accounts Payable'];
        foreach ($this->data['apAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Accounts Payable', '', 'Rp ' . number_format($this->data['totalAP'], 0, ',', '.')];

        $rows[] = ['  Accrued Expenses'];
        foreach ($this->data['accruedLiabilities'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Accrued Expenses', '', 'Rp ' . number_format($this->data['totalAccrued'], 0, ',', '.')];
        
        $rows[] = ['TOTAL CURRENT LIABILITIES', '', 'Rp ' . number_format($this->data['totalCurrentLiabilities'], 0, ',', '.')];
        $rows[] = [];

        // Equity
        $rows[] = ['Equity'];
        $rows[] = ['  Share Capital'];
        foreach ($this->data['equityAccounts'] as $acc) {
            $rows[] = ['    ' . $acc->name, $acc->code, 'Rp ' . number_format($acc->balance, 0, ',', '.')];
        }
        $rows[] = ['  Total Share Capital', '', 'Rp ' . number_format($this->data['totalShareCapital'], 0, ',', '.')];
        
        $rows[] = ['  Retained Earnings', '', 'Rp ' . number_format($this->data['retainedEarnings'], 0, ',', '.')];
        
        $rows[] = ['TOTAL EQUITY', '', 'Rp ' . number_format($this->data['totalEquity'], 0, ',', '.')];
        $rows[] = [];
        
        $rows[] = ['TOTAL LIABILITIES & EQUITY', '', 'Rp ' . number_format($this->data['totalLiabilitiesAndEquity'], 0, ',', '.')];

        return $rows;
    }

    public function title(): string
    {
        return 'Balance Sheet';
    }
}
