<?php

namespace App\Exports;

use App\Models\StockIn;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockInExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query ?: StockIn::query();
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

    public function map($stockIn): array
    {
        $this->index++;
        return [
            $this->index,
            $stockIn->trx_date->format('Y-m-d H:i:s'),
            ucfirst($stockIn->item_type),
            $stockIn->item_name,
            $stockIn->quantity,
            $stockIn->user?->name,
        ];
    }
}
