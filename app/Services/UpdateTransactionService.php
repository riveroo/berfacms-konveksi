<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Stock;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\DB;

class UpdateTransactionService
{
    public function update(Transaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {
            $oldType = $transaction->transaction_type;
            $newType = $data['transaction_type'] ?? $oldType;
            $newItemStatus = $data['item_status'] ?? $transaction->item_status;

            // Check stock if new type is direct_order
            if ($newType === 'direct_order') {
                $errors = [];
                foreach ($data['items'] as $index => $item) {
                    // For existing items in direct_order, we conceptually have the stock.
                    // But to be safe, we calculate available stock + old quantity (if restoring).
                    // Actually, we can just restore stock first, then check.
                }
            }

            $client = \App\Models\Client::firstOrCreate(
                ['phone_number' => $data['client_phone']],
                [
                    'client_name' => $data['client_name'],
                    'information' => $data['client_info'] ?? null
                ]
            );

            // Restore stocks from old details if it was direct_order
            if ($oldType === 'direct_order') {
                foreach ($transaction->details as $oldDetail) {
                    $stock = Stock::where('variant_id', $oldDetail->variant_id)
                        ->where('size_option_id', $oldDetail->size_option_id)
                        ->first();
                    if ($stock) {
                        $stock->increment('stock', $oldDetail->quantity);
                    }
                }
            }

            // Now check stock again for new items if direct_order
            if ($newType === 'direct_order') {
                $errors = [];
                foreach ($data['items'] as $index => $item) {
                    $stock = Stock::where('variant_id', $item['variant_id'])
                        ->where('size_option_id', $item['size_option_id'])
                        ->first();
                    
                    $availableStock = $stock ? $stock->stock : 0;
                    if ($availableStock < $item['qty']) {
                        $errors["items.{$index}.qty"] = [
                            "Stock not sufficient. Available: {$availableStock}"
                        ];
                    }
                }
                if (!empty($errors)) {
                    throw \Illuminate\Validation\ValidationException::withMessages($errors);
                }
            }

            $transaction->details()->delete();

            $totalPrice = 0;
            $itemsDiscount = 0;
            
            foreach ($data['items'] as $item) {
                $totalPrice += ($item['price'] * $item['qty']);
                $itemsDiscount += ($item['discount'] ?? 0);
            }
            
            $overallDiscount = $data['overall_discount'] ?? 0;
            $grandTotal = $totalPrice - ($itemsDiscount + $overallDiscount);

            $transaction->client_id = $client->id;
            $transaction->total_price = $totalPrice;
            $transaction->total_discount = $itemsDiscount + $overallDiscount;
            $transaction->grand_total = $grandTotal > 0 ? $grandTotal : 0;
            
            $transaction->transaction_type = $newType;
            if ($newType === 'direct_order') {
                $transaction->item_status = $newItemStatus;
            } else {
                $transaction->item_status = 'in_progress';
                $transaction->status = 'on progress';
            }

            // Status automation
            if ($newType === 'direct_order') {
                if ($transaction->payment_status === 'paid' && $newItemStatus === 'collected') {
                    $transaction->status = 'done';
                } else {
                    $transaction->status = 'on progress';
                }
            }

            $transaction->save();

            foreach ($data['items'] as $item) {
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => ($item['price'] * $item['qty']) - ($item['discount'] ?? 0),
                ]);

                // Reduce stock again if direct_order
                if ($newType === 'direct_order') {
                    $stock = Stock::where('variant_id', $item['variant_id'])
                        ->where('size_option_id', $item['size_option_id'])
                        ->first();
                    if ($stock) {
                        $stock->decrement('stock', $item['qty']);
                    }
                }
            }

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'Updated transaction details',
            ]);

            return $transaction;
        });
    }

    public function cancel(Transaction $transaction)
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->status = 'cancelled';
            $transaction->save();

            TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'Cancelled order',
            ]);

            return $transaction;
        });
    }
}
