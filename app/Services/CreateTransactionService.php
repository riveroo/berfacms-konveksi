<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\PreOrder;
use App\Models\PreOrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTransactionService
{
    public function execute(array $data)
    {
        $transactionType = $data['transaction_type'] ?? 'direct_order';

        // 1. Check stock availability for direct_order
        if ($transactionType === 'direct_order') {
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
                throw ValidationException::withMessages($errors);
            }
        }

        // 2. Save Data
        return DB::transaction(function () use ($data, $transactionType) {
            $client = Client::firstOrCreate(
                ['phone_number' => $data['client_phone']],
                [
                    'client_name' => $data['client_name'],
                    'information' => $data['client_info'] ?? null,
                ]
            );

            $totalPrice = 0;
            $minDiscount = 0;
            
            foreach ($data['items'] as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $totalPrice += $subtotal;
                $minDiscount += (($item['discount'] ?? 0) * $item['qty']);
            }
            
            $overallDiscount = $data['overall_discount'] ?? 0;
            if ($overallDiscount < $minDiscount) {
                throw ValidationException::withMessages([
                    'overall_discount' => ['Overall discount cannot be less than total item discounts (' . number_format($minDiscount, 0, ',', '.') . ').']
                ]);
            }
            
            $depositUsed = $client->customer_balance ?: 0;
            $grandTotal = $totalPrice - $overallDiscount - $depositUsed;

            $timezone = $data['device_timezone'] ?? config('app.timezone');
            // Dapatkan waktu lokal saat ini di timezone device klien
            $localTime = \Carbon\Carbon::now($timezone);
            // Karena Laravel menyinkronkan data datetime ke database menggunakan zona waktu UTC dari config app.php (default UTC),
            // kita harus mengemas waktu lokal ini ke dalam objek Carbon bertimezone UTC tanpa mengubah representasi jamnya.
            // Contoh: Jam 22:00 (Asia/Jakarta) -> Jam 22:00 (UTC).
            $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $localTime->toDateTimeString(), config('app.timezone'));

            $transaction = new Transaction([
                'trx_id' => 'TRX-' . strtoupper(uniqid()),
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => $overallDiscount,
                'customer_balance' => $depositUsed,
                'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
                'status' => 'on progress',
                'transaction_type' => $transactionType,
                'item_status' => 'in_progress',
                'payment_status' => 'unpaid',
            ]);
            $transaction->created_at = $now;
            $transaction->updated_at = $now;
            $transaction->save();

            if ($depositUsed > 0) {
                $client->customer_balance = 0;
                $client->save();
            }

            // If it's a pre_order, perhaps we also need a PreOrder record?
            // To make this work without breaking the existing PreOrder page, we create a PreOrder.
            $preOrder = null;
            if ($transactionType === 'pre_order') {
                $preOrder = new PreOrder([
                    'po_id' => 'PO-' . strtoupper(uniqid()),
                    'client_id' => $client->id,
                    'transaction_id' => $transaction->id,
                    'total_price' => $totalPrice,
                    'total_discount' => $overallDiscount,
                    'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
                    'status' => 'on process',
                ]);
                $preOrder->created_at = $now;
                $preOrder->updated_at = $now;
                $preOrder->save();
            }

            foreach ($data['items'] as $item) {
                $detail = new TransactionDetail([
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

                if ($preOrder) {
                    $poDetail = new PreOrderDetail([
                        'pre_order_id' => $preOrder->id,
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'],
                        'size_option_id' => $item['size_option_id'],
                        'price' => $item['price'],
                        'quantity' => $item['qty'],
                        'discount' => $item['discount'] ?? 0,
                        'subtotal' => $item['price'] * $item['qty'],
                    ]);
                    $poDetail->created_at = $now;
                    $poDetail->updated_at = $now;
                    $poDetail->save();
                }

                // 3. Stock deduction for direct_order
                if ($transactionType === 'direct_order') {
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

            $log = new \App\Models\TransactionLog([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'Created transaction',
            ]);
            $log->created_at = $now;
            $log->updated_at = $now;
            $log->save();

            return [
                'transaction' => $transaction,
                'preOrder' => $preOrder,
            ];
        });
    }
}
