<?php

namespace App\Exports;

use App\Models\StockOut;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockOutExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query ?: StockOut::query();
    }

    public function headings(): array
    {
        return [
            'No',
            'TRX Date',
            'Item Type',
            'Item Name',
            'Quantity',
            'User',
        ];
    }

    protected $index = 0;

    public function map($stockOut): array
    {
        $this->index++;
        return [
            $this->index,
            $stockOut->trx_date->format('Y-m-d H:i:s'),
            ucfirst($stockOut->item_type),
            $stockOut->item_name,
            $stockOut->quantity,
            $stockOut->user?->name,
        ];
    }
}
