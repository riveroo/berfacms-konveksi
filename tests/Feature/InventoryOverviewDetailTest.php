<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\ProductType;
use App\Models\Unit;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryOverviewDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Role $role;
    protected Item $item;
    protected Supplier $supplier;
    protected ProductType $productType;
    protected Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'Administrator', 'is_active' => true]);

        $permission = Permission::create([
            'menu_name' => 'Inventory Overview',
            'route' => 'inventory/overview',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Mega Textile Supplier',
            'contact' => 'Mega Boss',
        ]);

        $this->productType = ProductType::create(['name' => 'Silk']);
        $this->unit = Unit::create(['name' => 'yards']);

        $this->item = Item::create([
            'item_id' => 'ITM-TEST-001',
            'item_name' => 'Premium Pure Silk',
            'item_code' => 'SILK-PREM-001',
            'product_type_id' => $this->productType->id,
            'unit_id' => $this->unit->id,
            'minimum_stock' => 5,
            'price' => 125000,
            'stock' => 50,
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_authorized_user_can_view_inventory_overview_with_details_button(): void
    {
        $response = $this->actingAs($this->user)->get(route('inventory.overview'));

        $response->assertOk();
        $response->assertSee('ITM-TEST-001');
        $response->assertSee('Premium Pure Silk');
        $response->assertSee('Details');
        $response->assertSee(route('inventory.overview.detail', $this->item->id));
    }

    public function test_authorized_user_can_view_inventory_overview_details_page_with_stock_history(): void
    {
        // Let's seed a stock in record
        $stockIn = StockIn::create([
            'trx_date' => now()->subDay(),
            'item_type' => 'material',
            'item_id' => $this->item->id,
            'quantity' => 10,
            'user_id' => $this->user->id,
        ]);

        // Let's seed a stock out record
        $stockOut = StockOut::create([
            'trx_date' => now(),
            'item_type' => 'material',
            'item_id' => $this->item->id,
            'quantity' => 2,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('inventory.overview.detail', $this->item->id));

        $response->assertOk();

        // 1. Assert Item Information Section is displayed
        $response->assertSee('Item ID');
        $response->assertSee('ITM-TEST-001');
        $response->assertSee('Item Code');
        $response->assertSee('SILK-PREM-001');
        $response->assertSee('Mega Textile Supplier');
        $response->assertSee('Silk');
        $response->assertSee('yards');
        $response->assertSee('Rp 125.000');

        // 2. Assert Stock In history section is displayed
        $response->assertSee('Stock In History');
        $response->assertSee('+10');
        $response->assertSee($this->user->name);

        // 3. Assert Stock Out history section is displayed
        $response->assertSee('Stock Out History');
        $response->assertSee('-2');
    }
}
