<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class TransactionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear existing transaction details to avoid duplicates
        TransactionDetail::truncate();

        $transactions = Transaction::all();
        
        // Eager load variant to get product_id efficiently
        $stocks = Stock::with('variant')->get();

        if ($stocks->isEmpty()) {
            $this->command->warn('Skipping TransactionDetailSeeder: No stocks found in the database.');
            return;
        }

        if ($transactions->isEmpty()) {
            $this->command->warn('Skipping TransactionDetailSeeder: No transactions found in the database.');
            return;
        }

        foreach ($transactions as $transaction) {
            // Randomly decide how many items in this transaction (1 to 5)
            $itemCount = rand(1, 5);
            
            $totalPrice = 0;
            $itemsDiscount = 0;

            // Pick random unique stocks for this transaction
            $selectedStocks = $stocks->random(min($itemCount, $stocks->count()));

            foreach ($selectedStocks as $stock) {
                $quantity = rand(1, 10);
                $price = $stock->price;
                
                // Random small discount (e.g., multiples of 1000 or 500)
                $discount = rand(0, 5) === 0 ? rand(1, 5) * 1000 : 0; 
                
                $subtotal = ($price * $quantity) - $discount;

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $stock->variant->product_id,
                    'variant_id'     => $stock->variant_id,
                    'size_option_id' => $stock->size_option_id,
                    'price'          => $price,
                    'quantity'       => $quantity,
                    'discount'       => $discount,
                    'subtotal'       => max(0, $subtotal),
                ]);

                $totalPrice += ($price * $quantity);
                $itemsDiscount += $discount;
            }

            // Update the main transaction totals based on the seeded details
            $transaction->update([
                'total_price'    => $totalPrice,
                'total_discount' => $itemsDiscount,
                'grand_total'    => max(0, $totalPrice - $itemsDiscount),
            ]);
        }

        $this->command->info('TransactionDetailSeeder: ' . $transactions->count() . ' transactions populated with details.');
    }
}
