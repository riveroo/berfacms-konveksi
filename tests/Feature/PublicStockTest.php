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
}
