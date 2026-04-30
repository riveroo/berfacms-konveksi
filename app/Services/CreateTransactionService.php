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
            $itemsDiscount = 0;
            
            foreach ($data['items'] as $item) {
                $totalPrice += ($item['price'] * $item['qty']);
                $itemsDiscount += ($item['discount'] ?? 0);
            }
            
            $overallDiscount = $data['overall_discount'] ?? 0;
            $grandTotal = $totalPrice - ($itemsDiscount + $overallDiscount);

            $transaction = Transaction::create([
                'trx_id' => 'TRX-' . strtoupper(uniqid()),
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => $itemsDiscount + $overallDiscount,
                'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
                'status' => 'on progress',
                'transaction_type' => $transactionType,
                'item_status' => 'in_progress',
                'payment_status' => 'unpaid',
            ]);

            // If it's a pre_order, perhaps we also need a PreOrder record?
            // The user requested: "IF transaction_type = pre_order -> redirect to: /admin/pre-orders/{id}"
            // To make this work without breaking the existing PreOrder page, we create a PreOrder.
            $preOrder = null;
            if ($transactionType === 'pre_order') {
                $preOrder = PreOrder::create([
                    'po_id' => 'PO-' . strtoupper(uniqid()),
                    'client_id' => $client->id,
                    'transaction_id' => $transaction->id,
                    'total_price' => $totalPrice,
                    'total_discount' => $itemsDiscount + $overallDiscount,
                    'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
                    'status' => 'on process',
                ]);
            }

            foreach ($data['items'] as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => ($item['price'] * $item['qty']) - ($item['discount'] ?? 0),
                ]);

                if ($preOrder) {
                    PreOrderDetail::create([
                        'pre_order_id' => $preOrder->id,
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'],
                        'size_option_id' => $item['size_option_id'],
                        'price' => $item['price'],
                        'quantity' => $item['qty'],
                        'discount' => $item['discount'] ?? 0,
                        'subtotal' => ($item['price'] * $item['qty']) - ($item['discount'] ?? 0),
                    ]);
                }

                // 3. Stock deduction for direct_order
                if ($transactionType === 'direct_order') {
                    $stock = Stock::where('variant_id', $item['variant_id'])
                        ->where('size_option_id', $item['size_option_id'])
                        ->first();
                    if ($stock) {
                        $stock->decrement('stock', $item['qty']);
                    }
                }
            }

            \App\Models\TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'Created transaction',
            ]);

            return [
                'transaction' => $transaction,
                'preOrder' => $preOrder,
            ];
        });
    }
}
