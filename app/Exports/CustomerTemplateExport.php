<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['Customer Name', 'Phone Number', 'Description'];
    }

    public function array(): array
    {
        return [
            ['John Doe', '08123456789', 'Regular Customer'],
        ];
    }
}
