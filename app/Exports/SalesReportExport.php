<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tgl Trx',
            'No Invoice',
            'Nama Pelanggan',
            'Status Pembayaran',
            'Status Transaksi',
            'Total Harga (before discount)',
            'Diskon',
            'Grand Total',
            'Total Paid',
            'Admin',
        ];
    }

    public function map($transaction): array
    {
        static $row = 1;

        $totalHarga = $transaction->details->sum(function($detail) {
            return $detail->price * $detail->quantity;
        });

        $totalPaid = $transaction->payments->sum('amount');

        return [
            $row++,
            $transaction->created_at ? \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') : '-',
            $transaction->trx_id,
            $transaction->client->client_name ?? '-',
            ucfirst(str_replace('_', ' ', $transaction->payment_status)),
            ucfirst(str_replace('_', ' ', $transaction->status)),
            $totalHarga,
            $transaction->discount,
            $transaction->grand_total,
            $totalPaid,
            $transaction->logs->first()->user->name ?? '-',
        ];
    }
}
