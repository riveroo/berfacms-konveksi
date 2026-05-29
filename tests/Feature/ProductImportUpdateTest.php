<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductType;
use App\Models\SizeOption;
use App\Models\Stock;
use App\Imports\ProductsImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProductImportUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Role $role;
    protected Product $product;
    protected Variant $variant;
    protected ProductType $productType;
    protected SizeOption $sizeOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'Administrator', 'is_active' => true]);
        
        $importExportPermission = Permission::create([
            'menu_name' => 'Import Export',
            'route' => 'admin/import-export',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach([$importExportPermission->id]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
        ]);

        $this->productType = ProductType::create(['name' => 'T-Shirt']);

        $this->product = Product::create([
            'product_name' => 'Cotton Combed 30s',
            'is_active' => true,
        ]);

        $this->variant = Variant::create([
            'product_id' => $this->product->id,
            'product_type_id' => $this->productType->id,
            'variant_code' => 'TSHIRT-BLK-01',
            'variant_name' => 'Black Variant',
            'color' => '#000000',
        ]);

        $this->sizeOption = SizeOption::firstOrCreate(
            ['name' => 'L'],
            [
                'order' => 1,
                'status' => 'active',
            ]
        );
    }

    public function test_import_updates_existing_variant_when_product_name_variant_name_and_variant_code_match(): void
    {
        $import = new ProductsImport();

        // Let's import a row matching product_name, variant_name, variant_code
        // but with different color and product_type
        $rows = new Collection([
            [
                'product_name' => 'Cotton Combed 30s',
                'variant_name' => 'Black Variant',
                'variant_code' => 'TSHIRT-BLK-01',
                'color' => '#111111',
                'product_type' => 'Polo Shirt',
            ]
        ]);

        // Pre-count variants and stocks
        $initialVariantCount = Variant::count();
        $initialStockCount = Stock::count();

        $import->collection($rows);

        // Variant count should NOT change
        $this->assertEquals($initialVariantCount, Variant::count());
        $this->assertEquals($initialStockCount, Stock::count());

        // The existing variant should be updated
        $this->variant->refresh();
        $this->assertEquals('#111111', $this->variant->color);
        $this->assertEquals('Polo Shirt', $this->variant->productType->name);
    }

    public function test_import_inserts_new_variant_when_no_match(): void
    {
        $import = new ProductsImport();

        // Let's import a new row
        $rows = new Collection([
            [
                'product_name' => 'Cotton Combed 30s',
                'variant_name' => 'White Variant',
                'variant_code' => 'TSHIRT-WHT-02',
                'color' => '#FFFFFF',
                'product_type' => 'Polo Shirt',
            ]
        ]);

        $import->collection($rows);

        // A new variant should be created
        $this->assertEquals(2, Variant::count());
        
        $newVariant = Variant::where('variant_code', 'TSHIRT-WHT-02')->first();
        $this->assertNotNull($newVariant);
        $this->assertEquals('White Variant', $newVariant->variant_name);
        $this->assertEquals('#FFFFFF', $newVariant->color);
        $this->assertEquals('Polo Shirt', $newVariant->productType->name);

        // Stock row should be created for active sizes
        $this->assertDatabaseHas('stocks', [
            'variant_id' => $newVariant->id,
            'size_option_id' => $this->sizeOption->id,
            'stock' => 0,
        ]);
    }
}
