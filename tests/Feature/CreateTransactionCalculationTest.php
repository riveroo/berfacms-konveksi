<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Client;
use App\Models\Product;
use App\Models\Variant;
use App\Models\SizeOption;
use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTransactionCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Client $client;
    protected Product $product;
    protected Variant $variant;
    protected SizeOption $sizeOption;
    protected Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create([
            'name' => 'Finance Staff',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        // Create transaction permissions
        $permission = Permission::create([
            'menu_name' => 'Transactions',
            'route' => 'admin/transactions',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->client = Client::create([
            'client_name' => 'Alice Customer',
            'phone_number' => '08111111111',
            'address' => 'Customer Address 1',
        ]);

        $this->product = Product::create([
            'product_name' => 'Standard Polo Shirt',
            'is_active' => true,
        ]);

        $this->sizeOption = SizeOption::firstOrCreate([
            'name' => 'XL',
        ], [
            'order' => 1,
            'status' => 'active',
        ]);

        $this->variant = Variant::create([
            'product_id' => $this->product->id,
            'variant_code' => 'STD-POLO-BLK',
            'variant_name' => 'Black Polo',
            'color' => 'Black',
        ]);

        $this->stock = Stock::create([
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'stock' => 100,
            'price' => 100000.00,
        ]);
    }

    public function test_create_transaction_validates_overall_discount_limit(): void
    {
        $this->actingAs($this->user);

        // We have 2 items: 
        // 1. Qty = 2, Price = 100,000, Item Discount = 10,000 (total item discount = 20,000)
        // 2. Qty = 1, Price = 100,000, Item Discount = 5,000 (total item discount = 5,000)
        // Minimum overall discount must be 25,000.
        // If overall_discount is 20,000, it should fail.
        $response = $this->postJson(route('transactions.store'), [
            'client_phone' => '08111111111',
            'client_name' => 'Alice Customer',
            'transaction_type' => 'direct_order',
            'overall_discount' => 20000, // less than 25,000 min discount
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 2,
                    'price' => 100000,
                    'discount' => 10000,
                ],
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 1,
                    'price' => 100000,
                    'discount' => 5000,
                ],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['overall_discount']);
    }

    public function test_create_transaction_calculates_subtotal_and_grand_total_correctly(): void
    {
        $this->actingAs($this->user);

        // Subtotal should be: (100,000 * 2) + (100,000 * 1) = 300,000
        // Min discount = (10,000 * 2) + (5,000 * 1) = 25,000.
        // We set overall_discount to 30,000 (valid).
        // Grand total should be 300,000 - 30,000 = 270,000.
        $response = $this->postJson(route('transactions.store'), [
            'client_phone' => '08111111111',
            'client_name' => 'Alice Customer',
            'transaction_type' => 'direct_order',
            'overall_discount' => 30000,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 2,
                    'price' => 100000,
                    'discount' => 10000,
                ],
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 1,
                    'price' => 100000,
                    'discount' => 5000,
                ],
            ]
        ]);

        $response->assertStatus(200);

        $transaction = Transaction::first();
        $this->assertNotNull($transaction);
        
        // Check database metrics
        $this->assertEquals(300000.00, (float)$transaction->total_price); // subtotal sum
        $this->assertEquals(300000.00, (float)$transaction->details->sum('subtotal')); // details subtotal sum (each subtotal is price * qty)
        $this->assertEquals(30000.00, (float)$transaction->total_discount); // overall discount
        $this->assertEquals(270000.00, (float)$transaction->grand_total); // grand total
    }

    public function test_update_transaction_validates_overall_discount_limit(): void
    {
        $this->actingAs($this->user);

        // Create an existing transaction first
        $transaction = Transaction::create([
            'trx_id' => 'TRX-001',
            'client_id' => $this->client->id,
            'transaction_type' => 'direct_order',
            'total_price' => 100000,
            'total_discount' => 10000,
            'grand_total' => 90000,
            'status' => 'on progress',
            'payment_status' => 'unpaid',
            'item_status' => 'in_progress',
        ]);

        $transaction->details()->create([
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'price' => 100000,
            'quantity' => 1,
            'discount' => 10000,
            'subtotal' => 100000,
        ]);

        // Try to update with overall_discount < total item discounts
        // We have 2 items:
        // Item 1: Qty = 2, Price = 100k, Discount = 10k (total discount = 20k)
        // Item 2: Qty = 1, Price = 100k, Discount = 5k (total discount = 5k)
        // Total min discount = 25k. We send overall_discount = 20k, which should fail.
        $response = $this->putJson(route('transactions.update', $transaction->id), [
            'client_phone' => '08111111111',
            'client_name' => 'Alice Customer',
            'transaction_type' => 'direct_order',
            'item_status' => 'in_progress',
            'overall_discount' => 20000, // less than 25,000 min discount
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 2,
                    'price' => 100000,
                    'discount' => 10000,
                ],
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 1,
                    'price' => 100000,
                    'discount' => 5000,
                ],
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['overall_discount']);
    }

    public function test_update_transaction_calculates_subtotal_and_grand_total_correctly(): void
    {
        $this->actingAs($this->user);

        // Create an existing transaction first
        $transaction = Transaction::create([
            'trx_id' => 'TRX-002',
            'client_id' => $this->client->id,
            'transaction_type' => 'direct_order',
            'total_price' => 100000,
            'total_discount' => 10000,
            'grand_total' => 90000,
            'status' => 'on progress',
            'payment_status' => 'unpaid',
            'item_status' => 'in_progress',
        ]);

        $transaction->details()->create([
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'price' => 100000,
            'quantity' => 1,
            'discount' => 10000,
            'subtotal' => 100000,
        ]);

        // Update it with valid parameters
        // Items subtotal: (100k * 2) + (100k * 1) = 300k
        // Min discount: (10k * 2) + (5k * 1) = 25k
        // Overall discount set: 35k
        // Grand total: 300k - 35k = 265k
        $response = $this->putJson(route('transactions.update', $transaction->id), [
            'client_phone' => '08111111111',
            'client_name' => 'Alice Customer',
            'transaction_type' => 'direct_order',
            'item_status' => 'in_progress',
            'overall_discount' => 35000,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 2,
                    'price' => 100000,
                    'discount' => 10000,
                ],
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'size_option_id' => $this->sizeOption->id,
                    'qty' => 1,
                    'price' => 100000,
                    'discount' => 5000,
                ],
            ]
        ]);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals(300000.00, (float)$transaction->total_price);
        $this->assertEquals(35000.00, (float)$transaction->total_discount);
        $this->assertEquals(265000.00, (float)$transaction->grand_total);
    }
}
