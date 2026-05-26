<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Variant;
use App\Models\SizeOption;
use App\Models\Stock;
use App\Imports\ProductPricingImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Role;
use App\Models\Permission;

class ProductPricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Role $role;
    protected Product $product;
    protected Variant $variant;
    protected SizeOption $sizeOption;
    protected Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and assign permissions
        $this->role = Role::create(['name' => 'Administrator', 'is_active' => true]);
        
        $pricingPermission = Permission::create([
            'menu_name' => 'Product Pricing',
            'route' => 'admin/product-pricing',
            'can_access' => true,
        ]);

        $importExportPermission = Permission::create([
            'menu_name' => 'Import Export',
            'route' => 'admin/import-export',
            'can_access' => true,
        ]);

        $this->role->permissions()->attach([$pricingPermission->id, $importExportPermission->id]);

        // Create a test user with the authorized role
        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
        ]);

        // Create standard product setup
        $this->product = Product::firstOrCreate(
            ['product_code' => 'JKT-01'],
            [
                'product_name' => 'Premium Jacket',
                'is_active' => true,
            ]
        );

        $this->variant = Variant::firstOrCreate(
            ['variant_code' => 'JKT-01-BLK'],
            [
                'product_id' => $this->product->id,
                'variant_name' => 'Midnight Black',
                'color' => '#000000',
            ]
        );

        $this->sizeOption = SizeOption::firstOrCreate(
            ['name' => 'XL'],
            [
                'order' => 1,
                'is_active' => true,
            ]
        );

        $this->stock = Stock::firstOrCreate(
            [
                'variant_id' => $this->variant->id,
                'size_option_id' => $this->sizeOption->id,
            ],
            [
                'stock' => 15,
                'cogs' => 150000.00,
                'price' => 250000.00,
            ]
        );
    }

    /**
     * Test unauthenticated users are redirected.
     */
    public function test_unauthenticated_users_cannot_access_pricing_page(): void
    {
        $response = $this->get(route('admin.product-pricing'));
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test authenticated users can access the pricing page and view stocks.
     */
    public function test_authenticated_users_can_view_product_pricing_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.product-pricing'));

        $response->assertOk();
        $response->assertViewHas('stocks');
        $response->assertSee('Premium Jacket');
        $response->assertSee('Midnight Black');
        $response->assertSee('XL');
        $response->assertSee('Rp 150.000');
        $response->assertSee('Rp 250.000');
    }

    /**
     * Test single pricing update.
     */
    public function test_user_can_update_single_pricing_record(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.product-pricing.update', $this->stock->id), [
                'cogs' => 175000,
                'price' => 295000,
            ]);

        $response->assertRedirect();
        
        $this->stock->refresh();
        $this->assertEquals(175000.00, $this->stock->cogs);
        $this->assertEquals(295000.00, $this->stock->price);
    }

    /**
     * Test template download works and returns excel file.
     */
    public function test_user_can_download_pricing_template(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->user)
            ->get(route('admin.product-pricing.export'));

        $response->assertOk();
        Excel::assertDownloaded('product_pricing_template.xlsx');
    }

    /**
     * Test bulk import validation: successfully updates valid rows.
     */
    public function test_import_updates_cogs_and_price_when_all_details_match(): void
    {
        $import = new ProductPricingImport();

        // Create collection mimicking matching excel data row
        $rows = new Collection([
            [
                'stock_id' => $this->stock->id,
                'product_name' => 'Premium Jacket',
                'variant_name' => 'Midnight Black',
                'size' => 'XL',
                'cogs' => 180000.00,
                'price' => 310000.00,
            ]
        ]);

        $import->collection($rows);

        $this->stock->refresh();
        
        $this->assertEquals(1, $import->updatedCount);
        $this->assertEquals(0, $import->skippedCount);
        $this->assertEquals(180000.00, $this->stock->cogs);
        $this->assertEquals(310000.00, $this->stock->price);
    }

    /**
     * Test bulk import validation: skips row on product name mismatch.
     */
    public function test_import_skips_row_when_product_name_mismatches(): void
    {
        $import = new ProductPricingImport();

        $rows = new Collection([
            [
                'stock_id' => $this->stock->id,
                'product_name' => 'Wrong Product Name Here',
                'variant_name' => 'Midnight Black',
                'size' => 'XL',
                'cogs' => 180000.00,
                'price' => 310000.00,
            ]
        ]);

        $import->collection($rows);

        $this->stock->refresh();

        $this->assertEquals(0, $import->updatedCount);
        $this->assertEquals(1, $import->skippedCount);
        
        // Assert values remain unchanged
        $this->assertEquals(150000.00, $this->stock->cogs);
        $this->assertEquals(250000.00, $this->stock->price);
    }

    /**
     * Test bulk import validation: skips row on variant name mismatch.
     */
    public function test_import_skips_row_when_variant_name_mismatches(): void
    {
        $import = new ProductPricingImport();

        $rows = new Collection([
            [
                'stock_id' => $this->stock->id,
                'product_name' => 'Premium Jacket',
                'variant_name' => 'Shiny Gold',
                'size' => 'XL',
                'cogs' => 180000.00,
                'price' => 310000.00,
            ]
        ]);

        $import->collection($rows);

        $this->stock->refresh();

        $this->assertEquals(0, $import->updatedCount);
        $this->assertEquals(1, $import->skippedCount);
        
        // Assert values remain unchanged
        $this->assertEquals(150000.00, $this->stock->cogs);
        $this->assertEquals(250000.00, $this->stock->price);
    }

    /**
     * Test bulk import validation: skips row on size name mismatch.
     */
    public function test_import_skips_row_when_size_mismatches(): void
    {
        $import = new ProductPricingImport();

        $rows = new Collection([
            [
                'stock_id' => $this->stock->id,
                'product_name' => 'Premium Jacket',
                'variant_name' => 'Midnight Black',
                'size' => 'S', // wrong size
                'cogs' => 180000.00,
                'price' => 310000.00,
            ]
        ]);

        $import->collection($rows);

        $this->stock->refresh();

        $this->assertEquals(0, $import->updatedCount);
        $this->assertEquals(1, $import->skippedCount);
        
        // Assert values remain unchanged
        $this->assertEquals(150000.00, $this->stock->cogs);
        $this->assertEquals(250000.00, $this->stock->price);
    }

    /**
     * Test authenticated users with import-export permission can see the buttons.
     */
    public function test_import_export_buttons_are_visible_with_permission(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.product-pricing'));

        $response->assertOk();
        $response->assertSee('Download Template');
        $response->assertSee('Import Pricing');
    }

    /**
     * Test authenticated users without import-export permission cannot see the buttons.
     */
    public function test_import_export_buttons_are_hidden_without_permission(): void
    {
        // Create a role without import-export permission
        $limitedRole = Role::create(['name' => 'Staff', 'is_active' => true]);
        
        $pricingPermission = Permission::create([
            'menu_name' => 'Product Pricing Limited',
            'route' => 'admin/product-pricing',
            'can_access' => true,
        ]);

        $limitedRole->permissions()->attach([$pricingPermission->id]);

        $limitedUser = User::factory()->create([
            'role_id' => $limitedRole->id,
        ]);

        $response = $this->actingAs($limitedUser)->get(route('admin.product-pricing'));

        $response->assertOk();
        $response->assertDontSee('Download Template');
        $response->assertDontSee('Import Pricing');
    }
}
