<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Unit;
use App\Models\ProductType;
use App\Models\Supplier;
use App\Imports\ItemsImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ItemImportAndDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_inserts_new_item_and_resolves_relations(): void
    {
        $this->assertDatabaseCount('items', 0);

        // Pre-create some items to verify auto-increment ID format
        Item::create([
            'item_id' => 'ITM-0001',
            'item_name' => 'Previous Item',
            'item_code' => 'PREV-01',
            'price' => 1000,
        ]);

        $rows = new Collection([
            [
                'item_name' => 'Benang Jahit Hitam',
                'item_code' => 'BNG-001',
                'product_type' => 'Yarn',
                'unit' => 'Pcs',
                'supplier' => 'PT Sumber Benang',
                'price' => 12500,
            ]
        ]);

        $import = new ItemsImport();
        $import->collection($rows);

        $this->assertDatabaseCount('items', 2);
        
        $importedItem = Item::where('item_code', 'BNG-001')->first();
        $this->assertNotNull($importedItem);
        $this->assertEquals('ITM-0002', $importedItem->item_id);
        $this->assertEquals('Benang Jahit Hitam', $importedItem->item_name);
        $this->assertEquals(12500, (float)$importedItem->price);

        // Verify relationships resolved
        $this->assertEquals('Yarn', $importedItem->productType->name);
        $this->assertEquals('Pcs', $importedItem->unit->name);
        $this->assertEquals('PT Sumber Benang', $importedItem->supplier->name);
    }

    public function test_import_skips_duplicates(): void
    {
        // Pre-create the item
        Item::create([
            'item_id' => 'ITM-0001',
            'item_name' => 'Benang Jahit Hitam',
            'item_code' => 'BNG-001',
            'price' => 1000,
        ]);

        // Row with same name
        $rows = new Collection([
            [
                'item_name' => 'Benang Jahit Hitam',
                'item_code' => 'BNG-002',
                'product_type' => 'Yarn',
                'unit' => 'Pcs',
                'supplier' => 'PT Sumber Benang',
                'price' => 12500,
            ]
        ]);

        $import = new ItemsImport();
        $import->collection($rows);

        // Count should still be 1 (skipped because same item_name)
        $this->assertDatabaseCount('items', 1);

        // Row with same code
        $rows2 = new Collection([
            [
                'item_name' => 'Kancing Baju Kuning',
                'item_code' => 'BNG-001',
                'product_type' => 'Button',
                'unit' => 'Pcs',
                'supplier' => 'PT Sumber Benang',
                'price' => 12500,
            ]
        ]);

        $import->collection($rows2);

        // Count should still be 1 (skipped because same item_code)
        $this->assertDatabaseCount('items', 1);
    }

    public function test_item_cannot_be_deleted_if_referenced(): void
    {
        $item = Item::create([
            'item_id' => 'ITM-0001',
            'item_name' => 'Bahan Baku',
            'item_code' => 'BHN-01',
            'price' => 5000,
        ]);

        // It should be deletable initially
        $item->delete();
        $this->assertDatabaseCount('items', 0);

        // Create new item and reference it in a stock_in table
        $item2 = Item::create([
            'item_id' => 'ITM-0002',
            'item_name' => 'Bahan Baku 2',
            'item_code' => 'BHN-02',
            'price' => 5000,
        ]);

        $user = User::factory()->create();

        \DB::table('stock_ins')->insert([
            'trx_date' => now(),
            'item_type' => 'material',
            'item_id' => $item2->id,
            'quantity' => 10,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(\Exception::class);
        $item2->delete();
    }
}
