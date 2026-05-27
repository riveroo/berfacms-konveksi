<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Client;
use App\Models\Product;
use App\Models\Variant;
use App\Models\SizeOption;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionReportDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Client $client;
    protected Product $product;
    protected Variant $variant;
    protected SizeOption $sizeOption;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and user
        $this->role = Role::create([
            'name' => 'Accountant',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        // Create a client
        $this->client = Client::create([
            'client_name' => 'John Doe',
            'phone_number' => '08123456789',
            'address' => 'Test Address',
        ]);

        // Create a product
        $this->product = Product::create([
            'product_name' => 'Premium Shirt',
            'is_active' => true,
        ]);

        // Create variant and size option due to NOT NULL constraints
        $this->sizeOption = SizeOption::firstOrCreate([
            'name' => 'XL',
        ], [
            'order' => 1,
            'status' => 'active',
        ]);

        $this->variant = Variant::create([
            'product_id' => $this->product->id,
            'variant_code' => 'PREM-SHIRT-BLK',
            'variant_name' => 'Black Premium Shirt',
            'color' => 'Black',
        ]);
    }

    public function test_guest_cannot_access_transactions_dashboard(): void
    {
        $response = $this->get('/admin/transactions/report');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_transactions_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/transactions/report');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_transactions_dashboard(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Sales Dashboard',
            'route' => 'admin/transactions/report',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/transactions/report');
        $response->assertStatus(200);
        $response->assertViewIs('admin.transactions.report');
        $response->assertSee('Transactions Dashboard');
    }

    public function test_transactions_dashboard_calculates_correct_summary_metrics(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Sales Dashboard',
            'route' => 'admin/transactions/report',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Transaction 1
        $trx1 = new Transaction([
            'trx_id' => 'INV-001',
            'client_id' => $this->client->id,
            'total_price' => 1000000.00,
            'total_discount' => 0.00,
            'grand_total' => 1000000.00,
            'status' => 'paid',
        ]);
        $trx1->created_at = '2026-05-10 10:00:00';
        $trx1->save();
        TransactionDetail::create([
            'transaction_id' => $trx1->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'price' => 250000.00,
            'quantity' => 4,
            'subtotal' => 1000000.00,
        ]);

        // Transaction 2
        $trx2 = new Transaction([
            'trx_id' => 'INV-002',
            'client_id' => $this->client->id,
            'total_price' => 500000.00,
            'total_discount' => 0.00,
            'grand_total' => 500000.00,
            'status' => 'done',
        ]);
        $trx2->created_at = '2026-05-12 14:00:00';
        $trx2->save();
        TransactionDetail::create([
            'transaction_id' => $trx2->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'price' => 250000.00,
            'quantity' => 2,
            'subtotal' => 500000.00,
        ]);

        // Access dashboard for May 2026
        $response = $this->actingAs($this->user)->get('/admin/transactions/report?start_date=2026-05-01&end_date=2026-05-31');
        $response->assertStatus(200);

        // Revenue = 1,500,000
        $response->assertSee('Rp 1.500.000');
        // Total products sold = 6
        $response->assertSee('6 units');
        // Average order value = 750,000
        $response->assertSee('Rp 750.000');

        // Top Spenders table
        $response->assertSee('John Doe');
        $response->assertSee('Top Spenders');

        // Top Selling Products table
        $response->assertSee('Premium Shirt');
        $response->assertSee('Top-Selling Products');
    }

    public function test_transactions_dashboard_filters_out_unwanted_periods(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Sales Dashboard',
            'route' => 'admin/transactions/report',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // May sale
        $trxMay = new Transaction([
            'trx_id' => 'INV-MAY',
            'client_id' => $this->client->id,
            'total_price' => 800000.00,
            'total_discount' => 0.00,
            'grand_total' => 800000.00,
            'status' => 'paid',
        ]);
        $trxMay->created_at = '2026-05-15 10:00:00';
        $trxMay->save();
        TransactionDetail::create([
            'transaction_id' => $trxMay->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'price' => 800000.00,
            'quantity' => 1,
            'subtotal' => 800000.00,
        ]);

        // June sale
        $trxJune = new Transaction([
            'trx_id' => 'INV-JUN',
            'client_id' => $this->client->id,
            'total_price' => 1200000.00,
            'total_discount' => 0.00,
            'grand_total' => 1200000.00,
            'status' => 'paid',
        ]);
        $trxJune->created_at = '2026-06-05 14:00:00';
        $trxJune->save();
        TransactionDetail::create([
            'transaction_id' => $trxJune->id,
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'size_option_id' => $this->sizeOption->id,
            'price' => 1200000.00,
            'quantity' => 1,
            'subtotal' => 1200000.00,
        ]);

        // Filter only May 2026
        $response = $this->actingAs($this->user)->get('/admin/transactions/report?start_date=2026-05-01&end_date=2026-05-31');
        $response->assertSee('Rp 800.000');
        $response->assertDontSee('Rp 1.200.000');
    }
}
