<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;
    private int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Slugified headings support
            $name = isset($row['supplier_name']) ? trim($row['supplier_name']) : (isset($row['name']) ? trim($row['name']) : '');
            $contact = isset($row['contact']) ? trim($row['contact']) : '';
            $info = isset($row['information']) ? trim($row['information']) : '';
            $address = isset($row['address']) ? trim($row['address']) : '';

            if (empty($name)) {
                $this->skippedCount++;
                continue;
            }

            // Duplicate check: name already exists -> skip
            $exists = Supplier::where('name', $name)->exists();

            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            Supplier::create([
                'name' => $name,
                'contact' => $contact ?: null,
                'information' => $info ?: null,
                'address' => $address ?: null,
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
