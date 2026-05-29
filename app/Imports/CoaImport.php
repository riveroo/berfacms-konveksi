<?php

namespace App\Imports;

use App\Models\Account;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoaImport implements ToCollection, WithHeadingRow
{
    public int $createdCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $code = isset($row['code']) ? trim($row['code']) : '';
            $name = isset($row['name']) ? trim($row['name']) : '';
            $type = isset($row['type']) ? strtolower(trim($row['type'])) : '';
            $parentAccount = isset($row['parent_account']) ? trim($row['parent_account']) : '';

            if (empty($code) || empty($name) || empty($type)) {
                $this->skippedCount++;
                $this->errors[] = "Row " . ($index + 2) . ": Code, Name, and Type are required.";
                continue;
            }

            // Type validation
            if (!in_array($type, ['asset', 'liability', 'equity', 'revenue', 'expense'])) {
                $this->skippedCount++;
                $this->errors[] = "Row " . ($index + 2) . ": Invalid account type. Must be asset, liability, equity, revenue, or expense.";
                continue;
            }

            // Find parent if defined
            $parentId = null;
            if (!empty($parentAccount)) {
                $parent = Account::where('name', $parentAccount)
                    ->orWhere('code', $parentAccount)
                    ->first();
                if ($parent) {
                    $parentId = $parent->id;
                } else {
                    $this->errors[] = "Row " . ($index + 2) . ": Parent account '{$parentAccount}' not found. Created without parent.";
                }
            }

            // Create or update
            $account = Account::where('code', $code)->first();
            if ($account) {
                $account->update([
                    'name' => $name,
                    'type' => $type,
                    'parent_id' => $parentId,
                ]);
                $this->updatedCount++;
            } else {
                Account::create([
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                    'parent_id' => $parentId,
                    'is_active' => true,
                ]);
                $this->createdCount++;
            }
        }
    }
}
