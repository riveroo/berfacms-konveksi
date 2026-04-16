<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $productTypes = \App\Models\ProductType::pluck('id')->toArray();
        $units = \App\Models\Unit::pluck('id')->toArray();
        $suppliers = \App\Models\Supplier::pluck('id')->toArray();

        // If no constraints data, make fallback
        if (empty($productTypes)) {
            $pt = \App\Models\ProductType::create(['name' => 'Demo Type']);
            $productTypes[] = $pt->id;
        }
        if (empty($units)) {
            $u = \App\Models\Unit::create(['name' => 'Pcs']);
            $units[] = $u->id;
        }

        $fabricTypes = ['Cotton Fabric', 'Polyester', 'Silk', 'Denim', 'Wool', 'Linen', 'Spandex', 'Viscose', 'Velvet', 'Chiffon'];

        for ($i = 0; $i < 30; $i++) {
            \App\Models\Item::create([
                'item_id' => 'ITM-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'item_name' => $faker->randomElement($fabricTypes) . ' - ' . $faker->colorName,
                'item_code' => strtoupper($faker->bothify('???-###')),
                'product_type_id' => $faker->randomElement($productTypes),
                'unit_id' => $faker->randomElement($units),
                'minimum_stock' => $faker->numberBetween(5, 50),
                'price' => $faker->numberBetween(10, 500) * 1000, // E.g. 10000 to 500000
                'supplier_id' => empty($suppliers) ? null : $faker->randomElement($suppliers),
            ]);
        }
    }
}
