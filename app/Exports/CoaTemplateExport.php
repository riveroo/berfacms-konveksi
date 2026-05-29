<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoaTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['code', 'name', 'type', 'parent_account'];
    }

    public function array(): array
    {
        return [
            ['1001', 'Cash on Hand', 'asset', ''],
            ['1002', 'Bank BCA', 'asset', 'Cash on Hand'],
            ['3001', 'Capital', 'equity', ''],
        ];
    }
}
