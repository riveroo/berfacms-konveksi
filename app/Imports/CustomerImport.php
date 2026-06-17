<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;
    private int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Slugified headings support
            $name = isset($row['customer_name']) ? trim($row['customer_name']) : (isset($row['client_name']) ? trim($row['client_name']) : '');
            $phone = isset($row['phone_number']) ? trim($row['phone_number']) : '';
            $info = isset($row['description']) ? trim($row['description']) : (isset($row['information']) ? trim($row['information']) : '');

            if (empty($name)) {
                $this->skippedCount++;
                continue;
            }

            // Duplicate check: client_name + phone_number already exists -> skip
            $exists = Client::where('client_name', $name)
                ->where('phone_number', $phone)
                ->exists();

            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            Client::create([
                'client_name' => $name,
                'phone_number' => $phone,
                'information' => $info ?: null,
                'type' => 'customer',
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
