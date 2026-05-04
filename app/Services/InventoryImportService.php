<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryImportService
{
    protected $successCount = 0;
    protected $failedCount = 0;
    protected $failedRows = [];

    public function processRows($rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Maatwebsite Excel converts "ITEM-ID" heading to "item_id"
                $itemCode = $row['item_id'] ?? ($row['item_code'] ?? ($row['item_name'] ?? null));
                $stockQty = $row['stock'] ?? ($row['stock_qty'] ?? null);

                if (empty($itemCode) || !is_numeric($stockQty)) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $index + 2, 
                        'reason' => 'Missing ITEM-ID or invalid Stock'
                    ];
                    continue;
                }

                $item = Item::where('item_code', $itemCode)
                            ->orWhere('item_name', $itemCode)
                            ->first();

                if (!$item) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $index + 2, 
                        'reason' => "Item not found: {$itemCode}"
                    ];
                    continue;
                }

                $item->stock = $stockQty;
                $item->save();

                $this->successCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Inventory Import Failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function getResults()
    {
        return [
            'success' => $this->successCount,
            'failed'  => $this->failedCount,
            'errors'  => $this->failedRows,
        ];
    }
}
