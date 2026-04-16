<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreOrder;
use App\Models\PreOrderDetail;
use App\Models\Client;
use App\Models\Product;

class PreOrderSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $products = Product::with('variants.stocks.sizeOption')->get();

        if ($clients->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['on process', 'accepted', 'rejected'];

        for ($i = 1; $i <= 10; $i++) {
            $client = $clients->random();
            $status = collect($statuses)->random();
            
            $totalPrice = rand(500000, 2000000);
            $totalDiscount = rand(0, 50000);
            $grandTotal = $totalPrice - $totalDiscount;

            $preOrder = PreOrder::create([
                'po_id' => 'PO-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => $totalDiscount,
                'grand_total' => $grandTotal,
                'status' => $status,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Add 1-3 details
            $numDetails = rand(1, 3);
            for ($j = 0; $j < $numDetails; $j++) {
                $product = $products->random();
                $variant = $product->variants->first();
                if (!$variant) continue;
                $stock = $variant->stocks->first();
                if (!$stock) continue;

                $price = $stock->price ?? rand(50000, 150000);
                $quantity = rand(1, 5);
                $discount = rand(0, 10000);
                $subtotal = ($price * $quantity) - $discount;

                PreOrderDetail::create([
                    'pre_order_id' => $preOrder->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'size_option_id' => $stock->size_option_id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ]);
            }
        }
    }
}
