<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class GeneralLedgerExport implements FromArray, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [
            ['GENERAL LEDGER REPORT'],
            ['Account: ' . $this->data['account']->code . ' - ' . $this->data['account']->name],
            ['Period: ' . $this->data['period_label']],
            [],
            ['Trx Date', 'COA Code', 'Account Name', 'Debit', 'Credit', 'Balance'],
        ];

        foreach ($this->data['rows'] as $row) {
            $rows[] = [
                $row['date'],
                $row['code'],
                $row['name'],
                $row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-',
                $row['credit'] > 0 ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '-',
                'Rp ' . number_format($row['balance'], 0, ',', '.'),
            ];
        }

        $rows[] = [];
        $rows[] = [
            'Total',
            '',
            '',
            'Rp ' . number_format($this->data['totalDebit'], 0, ',', '.'),
            'Rp ' . number_format($this->data['totalCredit'], 0, ',', '.'),
            'Rp ' . number_format($this->data['endingBalance'], 0, ',', '.'),
        ];

        return $rows;
    }

    public function title(): string
    {
        return 'General Ledger';
    }
}
