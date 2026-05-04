<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryStockTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'ITEM-ID',
            'Item Name',
            'Stock'
        ];
    }

    public function collection()
    {
        return Item::select('item_code', 'item_name', 'stock')->get();
    }
}
