<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class EditProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_button_disabled_by_default_and_enabled_when_dirty(): void
    {
        // 1. Setup Role and Permissions
        $role = Role::create(['name' => 'Administrator', 'is_active' => true]);
        
        $permission1 = Permission::create([
            'menu_name' => 'Products',
            'route' => 'admin/products',
            'can_access' => true,
        ]);
        $role->permissions()->attach([$permission1->id]);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);

        // 2. Create Product
        $product = Product::create([
            'product_name' => 'Original T-Shirt',
            'description' => 'Original Description',
            'is_active' => true,
        ]);

        // 3. Test Livewire component
        $component = Livewire::test(EditProduct::class, [
            'record' => $product->id,
        ]);

        // Assert isFormDirty is false initially
        $this->assertFalse($component->instance()->isFormDirty());

        // Set name to a different value and assert dirty is true
        $component->set('data.product_name', 'New T-Shirt');
        $this->assertTrue($component->instance()->isFormDirty());

        // Set name back to original and assert dirty is false again
        $component->set('data.product_name', 'Original T-Shirt');
        $this->assertFalse($component->instance()->isFormDirty());

        // Set sort_order to a different value and assert dirty is true
        $component->set('data.sort_order', 5);
        $this->assertTrue($component->instance()->isFormDirty());

        // Set sort_order back to original (0) and assert dirty is false again
        $component->set('data.sort_order', 0);
        $this->assertFalse($component->instance()->isFormDirty());
    }
}
