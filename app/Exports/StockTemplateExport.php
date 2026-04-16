<?php

namespace App\Exports;

use App\Models\Variant;
use App\Models\SizeOption;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockTemplateExport implements FromCollection, WithHeadings
{
    protected $sizes;

    public function __construct()
    {
        $this->sizes = SizeOption::orderBy('id')->get();
    }

    public function collection()
    {
        $variants = Variant::with(['product', 'stocks'])->get();
        
        return $variants->map(function($variant) {
            $row = [
                'variant_id' => $variant->id,
                'variant_code' => $variant->variant_code,
                'product_name' => $variant->product ? $variant->product->product_name : '',
                'variant_name' => $variant->variant_name,
                'color' => $variant->color,
            ];

            foreach ($this->sizes as $size) {
                $stockItem = $variant->stocks->firstWhere('size_option_id', $size->id);
                $row[$size->name] = $stockItem ? $stockItem->stock : 0;
            }

            return collect($row);
        });
    }

    public function headings(): array
    {
        $headings = [
            'variant_id',
            'variant_code',
            'product_name',
            'variant_name',
            'color',
        ];

        foreach ($this->sizes as $size) {
            $headings[] = $size->name;
        }

        return $headings;
    }
}
