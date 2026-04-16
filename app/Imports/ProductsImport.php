<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Variant;
use App\Models\Stock;
use App\Models\SizeOption;
use App\Models\ProductType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            // Group by product_name
            $grouped = $rows->groupBy('product_name');

            foreach ($grouped as $productName => $productRows) {
                if (empty($productName)) continue;

                // Validate Duplicate
                if (Product::where('product_name', $productName)->exists()) {
                    throw new Exception("Product name already exists: {$productName}");
                }

                $firstRow = $productRows->first();

                // Create Product
                $product = Product::create([
                    'product_name' => $productName,
                    'description' => $firstRow['description'] ?? null,
                    'is_active' => true,
                ]);

                // Group by Variant Name
                $variantGroups = $productRows->groupBy('variant_name');

                foreach ($variantGroups as $variantName => $variantRows) {
                    if (empty($variantName)) continue;

                    $vFirstRow = $variantRows->first();

                    $productType = null;
                    if (!empty($vFirstRow['product_type'])) {
                        $pType = ProductType::firstOrCreate(['name' => $vFirstRow['product_type']]);
                        $productType = $pType->id;
                    }

                    $variant = Variant::create([
                        'product_id' => $product->id,
                        'product_type_id' => $productType,
                        'variant_code' => $vFirstRow['variant_code'] ?? null,
                        'variant_name' => $variantName,
                        'color' => $vFirstRow['color'] ?? null,
                    ]);

                    // Stocks
                    foreach ($variantRows as $row) {
                        if (empty($row['size'])) continue;

                        $sizeOpt = SizeOption::firstOrCreate(['name' => $row['size']]);

                        Stock::create([
                            'variant_id' => $variant->id,
                            'size_option_id' => $sizeOpt->id,
                            'stock' => intval($row['stock'] ?? 0),
                            'price' => floatval($row['price'] ?? 0),
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
