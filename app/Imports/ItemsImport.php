<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Unit;
use App\Models\ProductType;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $itemName = isset($row['item_name']) ? trim($row['item_name']) : '';
                if (empty($itemName)) {
                    continue;
                }

                $itemCode = isset($row['item_code']) ? trim($row['item_code']) : '';
                if (empty($itemCode)) {
                    $itemCode = 'CODE-' . strtoupper(Str::random(8));
                }

                // Check duplicate by item_code or item_name
                $exists = Item::where('item_code', $itemCode)
                    ->orWhere('item_name', $itemName)
                    ->exists();

                if ($exists) {
                    // Skip duplicate items
                    continue;
                }

                // Resolve Unit (required)
                $unitName = isset($row['unit']) ? trim($row['unit']) : '';
                if (empty($unitName)) {
                    continue;
                }
                $unit = Unit::firstOrCreate(['name' => $unitName]);

                // Resolve Product Type (optional)
                $productTypeName = isset($row['product_type']) ? trim($row['product_type']) : '';
                $productTypeId = null;
                if (!empty($productTypeName)) {
                    $pType = ProductType::firstOrCreate(['name' => $productTypeName]);
                    $productTypeId = $pType->id;
                }

                // Resolve Supplier (optional)
                $supplierName = isset($row['supplier']) ? trim($row['supplier']) : '';
                $supplierId = null;
                if (!empty($supplierName)) {
                    $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
                    $supplierId = $supplier->id;
                }

                // Resolve Price
                $price = isset($row['price']) ? (float)$row['price'] : 0.0;

                // Generate incremental item_id
                $lastItem = Item::where('item_id', 'like', 'ITM-%')
                    ->latest('id')
                    ->first();
                
                $nextNumber = 1;
                if ($lastItem) {
                    $parts = explode('-', $lastItem->item_id);
                    if (isset($parts[1]) && is_numeric($parts[1])) {
                        $nextNumber = (int)$parts[1] + 1;
                    }
                }
                $itemId = 'ITM-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Create Item
                Item::create([
                    'item_id' => $itemId,
                    'item_name' => $itemName,
                    'item_code' => $itemCode,
                    'product_type_id' => $productTypeId,
                    'unit_id' => $unit->id,
                    'supplier_id' => $supplierId,
                    'price' => $price,
                    'minimum_stock' => 0,
                    'stock' => 0,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
