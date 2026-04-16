<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryOverviewExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $supplierId;
    protected $productTypeId;

    public function __construct($search, $supplierId, $productTypeId)
    {
        $this->search = $search;
        $this->supplierId = $supplierId;
        $this->productTypeId = $productTypeId;
    }

    public function query()
    {
        $query = Item::with(['productType', 'unit'])->orderBy('item_name');

        if ($this->supplierId) {
            $query->where('supplier_id', $this->supplierId);
        }

        if ($this->productTypeId) {
            $query->where('product_type_id', $this->productTypeId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('item_name', 'like', "%{$this->search}%")
                  ->orWhere('item_code', 'like', "%{$this->search}%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Item Name',
            'Category',
            'Unit',
            'Current Stock',
            'Minimum Stock',
        ];
    }

    public function map($item): array
    {
        return [
            $item->item_code,
            $item->item_name,
            optional($item->productType)->name ?? '-',
            optional($item->unit)->name ?? '-',
            $item->stock,
            $item->minimum_stock,
        ];
    }
}
