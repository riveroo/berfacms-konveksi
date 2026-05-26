<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Product Sample', 
                'Red Variant', 
                'V-001', 
                '#FF0000', 
                '30s'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'product_name',
            'variant_name',
            'variant_code',
            'color',
            'product_type',
        ];
    }
}
