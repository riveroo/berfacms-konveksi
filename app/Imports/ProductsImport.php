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

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            // Pre-load all active size options
            $activeSizes = SizeOption::where('status', 'active')->get();

            foreach ($rows as $row) {
                $productName = isset($row['product_name']) ? trim($row['product_name']) : '';
                if (empty($productName)) {
                    continue;
                }

                // 1. Get or create the product based on product_name
                $product = Product::firstOrCreate(
                    ['product_name' => $productName],
                    [
                        'description' => null,
                        'is_active' => true,
                    ]
                );

                $variantName = isset($row['variant_name']) ? trim($row['variant_name']) : '';
                $variantCode = isset($row['variant_code']) ? trim($row['variant_code']) : '';
                
                if (empty($variantName)) {
                    continue;
                }

                // 2. Check if product name, variant name, and variant code already exist in database
                $existingVariant = null;
                if (!empty($variantCode)) {
                    $existingVariant = Variant::where('product_id', $product->id)
                        ->where('variant_name', $variantName)
                        ->where('variant_code', $variantCode)
                        ->first();
                }

                if ($existingVariant) {
                    // Resolve Product Type ID
                    $productTypeId = null;
                    $productTypeName = isset($row['product_type']) ? trim($row['product_type']) : '';
                    if (!empty($productTypeName)) {
                        $pType = ProductType::firstOrCreate(['name' => $productTypeName]);
                        $productTypeId = $pType->id;
                    }

                    $existingVariant->update([
                        'product_type_id' => $productTypeId,
                        'color' => isset($row['color']) ? trim($row['color']) : '#4F46E5',
                    ]);
                    continue;
                }

                // 3. Check variant code duplication under the same product (for other variants)
                $hasDuplicateCode = false;
                if (!empty($variantCode)) {
                    $hasDuplicateCode = Variant::where('product_id', $product->id)
                        ->where('variant_code', $variantCode)
                        ->exists();
                }

                if ($hasDuplicateCode) {
                    // Skip import for this variant row to prevent duplicates on the same product
                    continue;
                }

                // 4. Resolve Product Type ID
                $productTypeId = null;
                $productTypeName = isset($row['product_type']) ? trim($row['product_type']) : '';
                if (!empty($productTypeName)) {
                    $pType = ProductType::firstOrCreate(['name' => $productTypeName]);
                    $productTypeId = $pType->id;
                }

                // 5. Create Variant
                $variant = Variant::create([
                    'product_id' => $product->id,
                    'product_type_id' => $productTypeId,
                    'variant_code' => $variantCode ?: 'VAR-' . strtoupper(uniqid()),
                    'variant_name' => $variantName,
                    'color' => isset($row['color']) ? trim($row['color']) : '#4F46E5',
                ]);

                // 6. Automatically create stock rows for every active size option
                foreach ($activeSizes as $size) {
                    Stock::create([
                        'variant_id' => $variant->id,
                        'size_option_id' => $size->id,
                        'stock' => 0,
                        'price' => 0,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
