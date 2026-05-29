<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductType;
use App\Models\SizeOption;
use App\Livewire\ProductVariantsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class ProductVariantsManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_open_add_variant_modal(): void
    {
        $product = Product::create([
            'product_name' => 'T-Shirt',
            'is_active' => true,
        ]);

        $productType = ProductType::create([
            'name' => 'Kaos',
        ]);

        $size = SizeOption::firstOrCreate([
            'name' => 'L',
        ], [
            'order' => 1,
            'status' => 'active',
        ]);

        Livewire::test(ProductVariantsManager::class, ['record' => $product])
            ->assertSet('isModalOpen', false)
            ->call('openAddModal')
            ->assertSet('isModalOpen', true)
            ->assertSet('editingVariantId', null);
    }

    public function test_can_open_edit_variant_modal(): void
    {
        $product = Product::create([
            'product_name' => 'T-Shirt',
            'is_active' => true,
        ]);

        $productType = ProductType::create([
            'name' => 'Kaos',
        ]);

        $variant = Variant::create([
            'product_id' => $product->id,
            'variant_name' => 'Navy Blue L',
            'variant_code' => 'NV-L',
            'product_type_id' => $productType->id,
            'color' => '#0000FF',
        ]);

        Livewire::test(ProductVariantsManager::class, ['record' => $product])
            ->assertSet('isModalOpen', false)
            ->call('openEditModal', $variant->id)
            ->assertSet('isModalOpen', true)
            ->assertSet('editingVariantId', $variant->id)
            ->assertSet('variantName', 'Navy Blue L');
    }
}
