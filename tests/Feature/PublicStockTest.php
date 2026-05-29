<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_stock_page_loads_with_variants_list(): void
    {
        $product = Product::create([
            'product_name' => 'Kaos Polos',
            'is_active' => true,
        ]);

        $variant = Variant::create([
            'product_id' => $product->id,
            'variant_name' => 'Kaos Polos Merah L',
            'variant_code' => 'KPM-L',
            'color' => '#FF0000',
        ]);

        $response = $this->get('/stock');

        $response->assertOk();
        $response->assertViewHas('variantsList');
        $response->assertSee('Cari nama produk...');
        $response->assertSee('Hasil Pencarian Variant');
        
        $variantsList = $response->viewData('variantsList');
        $this->assertCount(1, $variantsList);
        $this->assertEquals('Kaos Polos Merah L', $variantsList[0]['variant_name']);
        $this->assertEquals('Kaos Polos', $variantsList[0]['product_name']);
    }

    public function test_public_stock_search_only_matches_variant_name_and_not_product_name(): void
    {
        $product = Product::create([
            'product_name' => 'Kaos Polos',
            'is_active' => true,
        ]);

        $variant = Variant::create([
            'product_id' => $product->id,
            'variant_name' => 'Linen Biru L',
            'variant_code' => 'LBL-01',
            'color' => '#0000FF',
        ]);

        // Search by product name 'Kaos' -> should return 0 results
        $response = $this->get('/stock?search=Kaos');
        $response->assertOk();
        $this->assertCount(0, $response->viewData('variants'));

        // Search by variant name 'Linen' -> should return 1 result
        $response = $this->get('/stock?search=Linen');
        $response->assertOk();
        $this->assertCount(1, $response->viewData('variants'));
    }
}
