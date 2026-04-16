<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['product_name' => 'Classic T-Shirt', 'description' => 'A comfortable, 100% cotton classic t-shirt everyday wear.'],
            ['product_name' => 'Denim Jacket', 'description' => 'Stylish and durable denim jacket with premium wash styling.'],
            ['product_name' => 'Slim Fit Jeans', 'description' => 'Modern slim fit jeans crafted with stretchable denim material.'],
        ];

        $variantTemplates = [
            ['variant_name' => 'Basic Black', 'color' => 'Black'],
            ['variant_name' => 'Classic White', 'color' => 'White'],
            ['variant_name' => 'Navy Blue', 'color' => 'Blue'],
        ];

        $sizes = ['S', 'M', 'L', 'XL'];

        foreach ($products as $pData) {
            $product = Product::create($pData);

            foreach ($variantTemplates as $vData) {
                // Attach at least 3 variants per product
                $variant = $product->variants()->create($vData);

                // Add sizes S, M, L, XL with random stock for each variant
                foreach ($sizes as $size) {
                    $sizeOption = \App\Models\SizeOption::firstOrCreate(['name' => $size]);

                    $variant->stocks()->create([
                        'size_option_id' => $sizeOption->id,
                        'stock' => rand(1, 100),
                        'price' => rand(10, 100) * 1000,
                    ]);
                }
            }
        }
    }
}
