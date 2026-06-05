<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class TrialBalanceExport implements FromArray, WithTitle
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
        $statusText = $this->data['isBalanced'] 
            ? __('finance.trial_balance_balanced') 
            : __('finance.trial_balance_out_of_balance') . ' (' . __('finance.difference') . ': Rp ' . number_format($this->data['difference'], 0, ',', '.') . ')';

        $rows = [
            [__('finance.trial_balance')],
            [__('finance.accounting_period') . ': ' . $this->data['period_label']],
            [__('master.validation_status') ?? 'Status: ' . $statusText],
            [],
            [__('finance.code') ?? 'Code', __('finance.name') ?? 'Name', __('finance.debit') ?? 'Debit', __('finance.credit') ?? 'Credit'],
        ];

        foreach ($this->data['rows'] as $row) {
            $name = $row['name'];
            if (!empty($row['parent_id'])) {
                $name = '    ' . $name;
            }
            $rows[] = [
                $row['code'],
                $name,
                $row['debit'] !== null ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-',
                $row['credit'] !== null ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '-',
            ];
        }

        $rows[] = [];
        $rows[] = [
            __('finance.total_summary') ?? 'Total Summary',
            '',
            'Rp ' . number_format($this->data['totalDebit'], 0, ',', '.'),
            'Rp ' . number_format($this->data['totalCredit'], 0, ',', '.'),
        ];

        return $rows;
    }

    public function title(): string
    {
        return __('finance.trial_balance');
    }
}
