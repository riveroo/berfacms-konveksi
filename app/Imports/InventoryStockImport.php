<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\InventoryImportService;

class InventoryStockImport implements ToCollection, WithHeadingRow
{
    protected $service;

    public function __construct(InventoryImportService $service)
    {
        $this->service = $service;
    }

    public function collection(Collection $rows)
    {
        $this->service->processRows($rows);
    }
}
