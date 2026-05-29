<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['Supplier Name', 'Contact', 'Information', 'Address'];
    }

    public function array(): array
    {
        return [
            ['ABC Textile', 'John Doe', 'Main Fabric Supplier', 'Jakarta'],
        ];
    }
}
