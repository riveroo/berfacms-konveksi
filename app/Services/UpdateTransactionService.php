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
                    $isOldItemService = \App\Models\Product::where('id', $oldDetail->product_id)->value('is_service') === 'yes';
                    if (!$isOldItemService) {
                        $stock = Stock::where('variant_id', $oldDetail->variant_id)
                            ->where('size_option_id', $oldDetail->size_option_id)
                            ->first();
                        if ($stock) {
                            $stock->increment('stock', $oldDetail->quantity);
                        }
                    }
                }
            }

            // Now check stock again for new items if direct_order
            if ($newType === 'direct_order') {
                $errors = [];
                foreach ($data['items'] as $index => $item) {
                    $isItemService = \App\Models\Product::where('id', $item['product_id'])->value('is_service') === 'yes';
                    if ($isItemService) {
                        continue; // Skip stock validation for services
                    }
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
            $minDiscount = 0;
            
            foreach ($data['items'] as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $totalPrice += $subtotal;
                $minDiscount += (($item['discount'] ?? 0) * $item['qty']);
            }
            
            $overallDiscount = $data['overall_discount'] ?? 0;
            if ($overallDiscount < $minDiscount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'overall_discount' => ['Overall discount cannot be less than total item discounts (' . number_format($minDiscount, 0, ',', '.') . ').']
                ]);
            }
            
            $grandTotal = $totalPrice - $overallDiscount;

            $timezone = $data['device_timezone'] ?? config('app.timezone');
            $localTime = \Carbon\Carbon::now($timezone);
            $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $localTime->toDateTimeString(), config('app.timezone'));

            $transaction->client_id = $client->id;
            $transaction->total_price = $totalPrice;
            $transaction->total_discount = $overallDiscount;
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

            $transaction->updated_at = $now;
            $transaction->save();

            foreach ($data['items'] as $item) {
                $detail = new \App\Models\TransactionDetail([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
                $detail->created_at = $now;
                $detail->updated_at = $now;
                $detail->save();

                // Reduce stock again if direct_order
                if ($newType === 'direct_order') {
                    $isItemService = \App\Models\Product::where('id', $item['product_id'])->value('is_service') === 'yes';
                    if (!$isItemService) {
                        $stock = Stock::where('variant_id', $item['variant_id'])
                            ->where('size_option_id', $item['size_option_id'])
                            ->first();
                        if ($stock) {
                            $stock->decrement('stock', $item['qty']);
                        }
                    }
                }
            }

            $log = new TransactionLog([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'Updated transaction details',
            ]);
            $log->created_at = $now;
            $log->updated_at = $now;
            $log->save();

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
