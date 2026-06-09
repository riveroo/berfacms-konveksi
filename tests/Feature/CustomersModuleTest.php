<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Livewire\Livewire;
use App\Filament\Pages\Customers;

class CustomersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create([
            'name' => 'Sales Admin',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        $this->client = Client::create([
            'client_name' => 'Alice Smith',
            'phone_number' => '08122334455',
            'address' => 'Customer Road 123',
            'type' => 'customer',
        ]);
    }

    public function test_guest_cannot_access_customers_page(): void
    {
        $response = $this->get('/admin/customers');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_customers_page(): void
    {
        // User has role but no permission assigned for Customers
        $response = $this->actingAs($this->user)->get('/admin/customers');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_customers_page(): void
    {
        // Give Customers permission
        $permission = Permission::create([
            'menu_name' => 'Customers',
            'route' => 'admin/customers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/customers');
        $response->assertOk();
    }

    public function test_customers_page_calculates_summaries_correctly(): void
    {
        // Give Customers permission
        $permission = Permission::create([
            'menu_name' => 'Customers',
            'route' => 'admin/customers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->actingAs($this->user);

        // Create 2 active transactions
        $t1 = Transaction::create([
            'client_id' => $this->client->id,
            'trx_id' => 'INV0526-00001',
            'total_price' => 100000,
            'total_discount' => 0,
            'grand_total' => 100000,
            'status' => 'waiting for payment',
        ]);

        $t2 = Transaction::create([
            'client_id' => $this->client->id,
            'trx_id' => 'INV0526-00002',
            'total_price' => 200000,
            'total_discount' => 0,
            'grand_total' => 200000,
            'status' => 'waiting for payment',
        ]);

        // Add payment to t1
        TransactionPayment::create([
            'transaction_id' => $t1->id,
            'amount' => 40000,
            'payment_date' => now(),
            'bank_name' => 'Bank BCA',
            'account_number' => '1234567890',
            'created_by' => $this->user->id,
        ]);

        // Access the page component
        Livewire::test(Customers::class)
            ->assertCanSeeTableRecords([$this->client])
            ->assertSee('Alice Smith')
            ->assertSee('2')
            ->assertSee('Rp 300.000')
            ->assertSee('Rp 260.000');
    }

    public function test_customer_import_can_process_rows(): void
    {
        // Define mock rows
        $rows = new \Illuminate\Support\Collection([
            [
                'customer_name' => 'Budi Santoso',
                'phone_number' => '081234567890',
                'description' => 'Pelanggan tetap',
            ],
            [
                'customer_name' => 'Alice Smith', // Already exists in setUp, should be skipped
                'phone_number' => '08122334455',
                'description' => 'Regular Customer',
            ],
            [
                'customer_name' => '', // Blank name, should be skipped
                'phone_number' => '08122334466',
                'description' => 'Skip me',
            ]
        ]);

        $import = new \App\Imports\CustomerImport();
        $import->collection($rows);

        $this->assertEquals(1, $import->getImportedCount());
        $this->assertEquals(2, $import->getSkippedCount());

        $this->assertDatabaseHas('clients', [
            'client_name' => 'Budi Santoso',
            'phone_number' => '081234567890',
            'information' => 'Pelanggan tetap',
            'type' => 'customer',
        ]);
    }

    public function test_authorized_user_can_edit_customer(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Customers',
            'route' => 'admin/customers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->actingAs($this->user);

        Livewire::test(Customers::class)
            ->callTableAction('edit', $this->client, [
                'client_name' => 'Alice Updated',
                'phone_number' => '08999999999',
                'email' => 'alice@updated.com',
                'information' => 'Some updated notes',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('clients', [
            'id' => $this->client->id,
            'client_name' => 'Alice Updated',
            'phone_number' => '08999999999',
            'email' => 'alice@updated.com',
            'type' => 'customer',
            'information' => 'Some updated notes',
        ]);
    }

    public function test_authorized_user_can_create_customer(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Customers',
            'route' => 'admin/customers',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $this->actingAs($this->user);

        Livewire::test(Customers::class)
            ->callAction('create', [
                'client_name' => 'Charlie New',
                'phone_number' => '08777777777',
                'email' => 'charlie@new.com',
                'information' => 'Created via header action',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('clients', [
            'client_name' => 'Charlie New',
            'phone_number' => '08777777777',
            'email' => 'charlie@new.com',
            'type' => 'customer',
            'information' => 'Created via header action',
        ]);
    }
}
