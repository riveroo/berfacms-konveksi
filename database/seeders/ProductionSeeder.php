<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\ProductionProduct;
use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use App\Services\StockMovementService;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        $stockService = new StockMovementService();

        // Material items
        $materials = Item::whereHas('productType', function($q) {
            $q->where('name', 'Material');
        })->get();

        if ($materials->isEmpty()) {
            $materials = Item::take(2)->get();
        }

        // Product stocks
        $stocks = Stock::with(['variant.product'])->where('stock', '>', 50)->take(5)->get();

        if ($stocks->isEmpty()) {
            $stocks = Stock::with(['variant.product'])->take(3)->get();
        }

        // Create 3 production records
        for ($i = 1; $i <= 3; $i++) {
            $date = now()->subDays(4 - $i);
            $todayStr = $date->format('dmY');
            $batchCode = $todayStr . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);

            $production = Production::create([
                'production_date' => $date,
                'batch_code' => $batchCode,
                'production_name' => "Production Batch " . chr(64 + $i),
                'user_id' => $user->id,
                'created_at' => $date,
            ]);

            // Add 2 materials consumption
            foreach ($materials->random(min(2, $materials->count())) as $mat) {
                $qty = rand(5, 15);
                ProductionMaterial::create([
                    'production_id' => $production->id,
                    'item_id' => $mat->id,
                    'quantity' => $qty,
                ]);

                // Update stock logic
                try {
                    $stockService->decrementMaterialStock($mat->id, $qty);
                } catch (\Exception $e) {
                    // Silently fail if stock insufficient during seeding
                }
            }

            // Add 2 products output
            foreach ($stocks->random(min(2, $stocks->count())) as $stock) {
                $qty = rand(10, 30);
                ProductionProduct::create([
                    'production_id' => $production->id,
                    'product_id' => $stock->variant->product_id,
                    'variant_id' => $stock->variant_id,
                    'size_option_id' => $stock->size_option_id,
                    'quantity' => $qty,
                ]);

                // Update stock logic
                $stockService->incrementProductStock($stock->variant_id, $stock->size_option_id, $qty);
            }
        }
    }
}
