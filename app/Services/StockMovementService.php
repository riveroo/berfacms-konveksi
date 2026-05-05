<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Exception;

class StockMovementService
{
    /**
     * Update stock for a given record (StockIn or StockOut)
     * 
     * @param mixed $record The record instance
     * @param int $direction 1 for In (increase), -1 for Out (decrease)
     * @param int|null $overrideQuantity Use this instead of record's quantity if provided
     */
    public function adjustStock($record, int $direction, int $overrideQuantity = null)
    {
        $quantity = $overrideQuantity ?? $record->quantity;
        $amount = $quantity * $direction;

        if ($record->item_type === 'material' && $record->item_id) {
            $item = Item::find($record->item_id);
            if ($item) {
                // For stock out, check if enough stock
                if ($direction === -1 && $item->stock < $quantity) {
                    throw new Exception("Stock not sufficient for material: {$item->item_name}");
                }
                $item->increment('stock', $amount);
            }
        }

        if ($record->item_type === 'product' && $record->variant_id) {
            $stock = Stock::where('variant_id', $record->variant_id)
                ->where('size_option_id', $record->size_option_id)
                ->first();
            
            if ($stock) {
                // For stock out, check if enough stock
                if ($direction === -1 && $stock->stock < $quantity) {
                    throw new Exception("Stock not sufficient for variant: {$record->item_name}");
                }
                $stock->increment('stock', $amount);
            } else if ($direction === -1) {
                throw new Exception("Stock record not found for variant: {$record->item_name}");
            }
        }
    }

    /**
     * Check if stock is sufficient for a reduction
     */
    public function checkSufficiency($itemType, $productId, $variantId, $sizeOptionId, $itemId, $quantity)
    {
        if ($itemType === 'material') {
            $item = Item::find($itemId);
            return $item && $item->stock >= $quantity;
        }

        if ($itemType === 'product') {
            $stock = Stock::where('variant_id', $variantId)
                ->where('size_option_id', $sizeOptionId)
                ->first();
            return $stock && $stock->stock >= $quantity;
        }

        return false;
    }
    public function getCurrentStock($itemType, $variantId, $sizeOptionId, $itemId)
    {
        if ($itemType === 'material') {
            $item = Item::find($itemId);
            return $item ? $item->stock : 0;
        }

        if ($itemType === 'product') {
            $stock = Stock::where('variant_id', $variantId)
                ->where('size_option_id', $sizeOptionId)
                ->first();
            return $stock ? $stock->stock : 0;
        }

        return 0;
    }

    public function performAdjustment($itemType, $variantId, $sizeOptionId, $itemId, $newStock)
    {
        if ($itemType === 'material') {
            $item = Item::find($itemId);
            if ($item) {
                $item->update(['stock' => $newStock]);
            }
        }

        if ($itemType === 'product') {
            $stock = Stock::where('variant_id', $variantId)
                ->where('size_option_id', $sizeOptionId)
                ->first();
            if ($stock) {
                $stock->update(['stock' => $newStock]);
            }
        }
    }

    public function decrementMaterialStock($itemId, $quantity)
    {
        $item = Item::findOrFail($itemId);
        if ($item->stock < $quantity) {
            throw new Exception("Stock not sufficient for material: {$item->item_name}");
        }
        $item->decrement('stock', $quantity);
    }

    public function incrementProductStock($variantId, $sizeOptionId, $quantity)
    {
        $stock = Stock::where('variant_id', $variantId)
            ->where('size_option_id', $sizeOptionId)
            ->first();
        
        if ($stock) {
            $stock->increment('stock', $quantity);
        } else {
            // If stock record doesn't exist, we might need to create one if that's the logic
            // But usually we assume variants have stock records.
            throw new Exception("Stock record not found for variant ID: {$variantId}");
        }
    }
}
