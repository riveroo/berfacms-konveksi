<?php

namespace App\Imports;

use App\Models\Stock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductPricingImport implements ToCollection, WithHeadingRow
{
    public int $updatedCount = 0;
    public int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $stockId = isset($row['stock_id']) ? intval($row['stock_id']) : null;
            if (!$stockId) {
                $this->skippedCount++;
                continue;
            }

            // Find stock with relations
            $stock = Stock::with(['variant.product', 'sizeOption'])->find($stockId);

            if (!$stock) {
                $this->skippedCount++;
                continue;
            }

            // Verify: Product Name, Variant Name, Size
            $dbProductName = optional(optional($stock->variant)->product)->product_name;
            $dbVariantName = optional($stock->variant)->variant_name;
            $dbSize = optional($stock->sizeOption)->name;

            $excelProductName = isset($row['product_name']) ? trim($row['product_name']) : '';
            $excelVariantName = isset($row['variant_name']) ? trim($row['variant_name']) : '';
            $excelSize = isset($row['size']) ? trim($row['size']) : '';

            // Perform robust verification (case-insensitive and whitespace trimmed)
            $isProductMatched = strtolower(trim($dbProductName)) === strtolower(trim($excelProductName));
            $isVariantMatched = strtolower(trim($dbVariantName)) === strtolower(trim($excelVariantName));
            $isSizeMatched = strtolower(trim($dbSize)) === strtolower(trim($excelSize));

            if (!$isProductMatched || !$isVariantMatched || !$isSizeMatched) {
                // Verification mismatch - Skip update
                $this->skippedCount++;
                continue;
            }

            // All matched successfully - Update values
            $stock->update([
                'cogs' => isset($row['cogs']) ? floatval($row['cogs']) : 0,
                'price' => isset($row['price']) ? floatval($row['price']) : 0,
            ]);

            $this->updatedCount++;
        }
    }
}
