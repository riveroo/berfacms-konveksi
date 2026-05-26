<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductPricingTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Return all stocks.
     */
    public function collection()
    {
        return Stock::with(['variant.product', 'sizeOption'])->get();
    }

    /**
     * Heading row for Excel.
     */
    public function headings(): array
    {
        return [
            'stock_id',
            'product_name',
            'variant_name',
            'size',
            'cogs',
            'price',
        ];
    }

    /**
     * Map each stock record.
     */
    public function map($stock): array
    {
        return [
            $stock->id,
            optional(optional($stock->variant)->product)->product_name ?? '-',
            optional($stock->variant)->variant_name ?? '-',
            optional($stock->sizeOption)->name ?? '-',
            $stock->cogs,
            $stock->price,
        ];
    }
}
