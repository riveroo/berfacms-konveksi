<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\ProductType;
use App\Models\Unit;
use App\Filament\Resources\SupplierResource;
use App\Filament\Resources\SupplierResource\Pages\ViewSupplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;

class SuppliersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create([
            'name' => 'Inventory Manager',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'ABC Textile',
            'contact' => 'John Doe',
            'information' => 'Fabric supply',
            'address' => 'Jakarta',
        ]);
    }

    public function test_guest_cannot_access_suppliers_module(): void
    {
        $response = $this->get('/admin/suppliers');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_suppliers_module(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/suppliers');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_suppliers_module(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Suppliers',
            'route' => 'admin/suppliers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/suppliers');
        $response->assertOk();
    }

    public function test_suppliers_page_calculates_correct_summary_columns(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Suppliers',
            'route' => 'admin/suppliers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $productType = ProductType::create(['name' => 'Fabric']);
        $unit = Unit::create(['name' => 'meters']);

        // Create 2 items for this supplier
        Item::create([
            'item_id' => 'ITM-0001',
            'item_name' => 'Cotton Fabric',
            'item_code' => 'COTTON-001',
            'product_type_id' => $productType->id,
            'unit_id' => $unit->id,
            'supplier_id' => $this->supplier->id,
            'minimum_stock' => 10,
            'price' => 50000,
        ]);

        Item::create([
            'item_id' => 'ITM-0002',
            'item_name' => 'Polyester Fabric',
            'item_code' => 'POLYESTER-001',
            'product_type_id' => $productType->id,
            'unit_id' => $unit->id,
            'supplier_id' => $this->supplier->id,
            'minimum_stock' => 20,
            'price' => 75000,
        ]);

        $this->actingAs($this->user);

        // Access the Supplier list page component
        Livewire::test(SupplierResource\Pages\ListSuppliers::class)
            ->assertCanSeeTableRecords([$this->supplier])
            ->assertSee('ABC Textile')
            ->assertSee('John Doe')
            ->assertSee('Jakarta')
            ->assertSee('2'); // Total Items
    }

    public function test_can_add_item_to_supplier_via_detail_page(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Suppliers',
            'route' => 'admin/suppliers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $productType = ProductType::create(['name' => 'Fabric']);
        $unit = Unit::create(['name' => 'meters']);

        $this->actingAs($this->user);

        // Test the ViewSupplier component table action
        Livewire::test(ViewSupplier::class, ['record' => $this->supplier->id])
            ->callTableAction('addItem', null, [
                'item_name' => 'Linen Fabric',
                'item_code' => 'LINEN-001',
                'product_type_id' => $productType->id,
                'unit_id' => $unit->id,
                'supplier_id' => $this->supplier->id,
                'minimum_stock' => 15,
                'price' => 90000,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('items', [
            'item_name' => 'Linen Fabric',
            'item_code' => 'LINEN-001',
            'supplier_id' => $this->supplier->id,
            'price' => 90000,
        ]);
    }

    public function test_supplier_import_processes_rows_and_skips_duplicates(): void
    {
        $rows = new \Illuminate\Support\Collection([
            [
                'supplier_name' => 'XYZ Fabrics',
                'contact' => 'Jane Doe',
                'information' => 'Premium silk',
                'address' => 'Bandung',
            ],
            [
                'supplier_name' => 'ABC Textile', // Duplicate name, should be skipped
                'contact' => 'Duplicate John',
                'information' => 'Should skip',
                'address' => 'Jakarta',
            ]
        ]);

        $import = new \App\Imports\SupplierImport();
        $import->collection($rows);

        $this->assertEquals(1, $import->getImportedCount());
        $this->assertEquals(1, $import->getSkippedCount());

        $this->assertDatabaseHas('suppliers', [
            'name' => 'XYZ Fabrics',
            'contact' => 'Jane Doe',
            'information' => 'Premium silk',
            'address' => 'Bandung',
        ]);
    }
}
