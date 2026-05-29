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
use App\Models\TransactionPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountsReceivableTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Client $client1;
    protected Client $client2;
    protected Product $product;
    protected Variant $variant;
    protected SizeOption $sizeOption;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and user
        $this->role = Role::create([
            'name' => 'Finance Staff',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        // Create clients
        $this->client1 = Client::create([
            'client_name' => 'Alice Customer',
            'phone_number' => '08111111111',
            'address' => 'Customer Address 1',
        ]);

        $this->client2 = Client::create([
            'client_name' => 'Bob Customer',
            'phone_number' => '08222222222',
            'address' => 'Customer Address 2',
        ]);

        // Create product, size option, and variant (due to NOT NULL database constraints)
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
    }

    public function test_guest_cannot_access_accounts_receivable(): void
    {
        $response = $this->get('/admin/accounts-receivable');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_accounts_receivable(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/accounts-receivable');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_accounts_receivable(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Accounts Receivable',
            'route' => 'admin/accounts-receivable',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->user->refresh();
        $this->user->unsetRelation('role');

        $response = $this->actingAs($this->user)->get('/admin/accounts-receivable');
        $response->assertStatus(200);
        $response->assertSee('Accounts Receivable');
    }

    public function test_accounts_receivable_calculates_correct_summary_metrics_and_table_data(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Accounts Receivable',
            'route' => 'admin/accounts-receivable',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Transaction 1 (Alice): Total = 1,000,000, Paid = 400,000, Outstanding = 600,000
        $trx1 = new Transaction([
            'trx_id' => 'INV-AR-001',
            'client_id' => $this->client1->id,
            'total_price' => 1000000.00,
            'total_discount' => 0.00,
            'grand_total' => 1000000.00,
            'status' => 'paid',
        ]);
        $trx1->save();

        TransactionPayment::create([
            'transaction_id' => $trx1->id,
            'amount' => 400000.00,
            'payment_date' => now(),
            'bank_name' => 'Bank Mandiri',
            'account_number' => '1234567890',
            'created_by' => $this->user->id,
        ]);

        // Transaction 2 (Bob): Total = 500,000, Paid = 500,000, Outstanding = 0
        $trx2 = new Transaction([
            'trx_id' => 'INV-AR-002',
            'client_id' => $this->client2->id,
            'total_price' => 500000.00,
            'total_discount' => 0.00,
            'grand_total' => 500000.00,
            'status' => 'done',
        ]);
        $trx2->save();

        TransactionPayment::create([
            'transaction_id' => $trx2->id,
            'amount' => 500000.00,
            'payment_date' => now(),
            'bank_name' => 'Bank BCA',
            'account_number' => '0987654321',
            'created_by' => $this->user->id,
        ]);

        $this->user->refresh();
        $this->user->unsetRelation('role');

        // Access page
        $response = $this->actingAs($this->user)->get('/admin/accounts-receivable');
        $response->assertStatus(200);

        // Verify table content
        $response->assertSee('Alice Customer');
        $response->assertSee('Bob Customer');
        $response->assertSee('Rp 1.000.000');
        $response->assertSee('Rp 400.000');
        $response->assertSee('Rp 600.000');
        $response->assertSee('Rp 500.000');

        // Verify summary cards totals using Livewire widget test:
        // Total Transactions = 1,500,000
        // Total Payments = 900,000
        // Outstanding Receivables = 600,000
        \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Filament\Widgets\AccountsReceivableOverview::class)
            ->assertSee('Rp 1.500.000')
            ->assertSee('Rp 900.000')
            ->assertSee('Rp 600.000');
    }

    public function test_accounts_receivable_filters_by_payment_status(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Accounts Receivable',
            'route' => 'admin/accounts-receivable',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Transaction 1 (Alice): Unpaid (grand_total = 1000000, payment = 0)
        $trx1 = new Transaction([
            'trx_id' => 'INV-AR-003',
            'client_id' => $this->client1->id,
            'total_price' => 1000000.00,
            'total_discount' => 0.00,
            'grand_total' => 1000000.00,
            'status' => 'paid',
        ]);
        $trx1->save();

        // Transaction 2 (Bob): Paid (grand_total = 500000, payment = 500000)
        $trx2 = new Transaction([
            'trx_id' => 'INV-AR-004',
            'client_id' => $this->client2->id,
            'total_price' => 500000.00,
            'total_discount' => 0.00,
            'grand_total' => 500000.00,
            'status' => 'done',
        ]);
        $trx2->save();

        TransactionPayment::create([
            'transaction_id' => $trx2->id,
            'amount' => 500000.00,
            'payment_date' => now(),
            'bank_name' => 'Bank BCA',
            'account_number' => '0987654321',
            'created_by' => $this->user->id,
        ]);

        $this->user->refresh();
        $this->user->unsetRelation('role');

        // Filter unpaid (Outstanding > 0) -> should see Alice, should not see Bob
        // Livewire table filtering uses tableFilters state. In Filament, we can test it using Livewire component methods or by accessing with filter params.
        // Let's verify by mounting the Livewire component!
        \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Filament\Pages\AccountsReceivable::class)
            ->assertSee('Alice Customer')
            ->assertSee('Bob Customer')
            ->set('tableFilters.payment_status.value', 'unpaid')
            ->assertSee('Alice Customer')
            ->assertDontSee('Bob Customer')
            ->set('tableFilters.payment_status.value', 'paid')
            ->assertDontSee('Alice Customer')
            ->assertSee('Bob Customer');
    }
}
