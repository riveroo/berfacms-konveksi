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
use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\JournalEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionPaymentAutoCashBookTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Client $client;
    protected Product $product;
    protected Variant $variant;
    protected SizeOption $sizeOption;
    protected Account $assetAccount;
    protected Account $categoryAccount;

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

        // Create transaction permission
        $permission = Permission::create([
            'menu_name' => 'Transactions',
            'route' => 'admin/transactions',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Create client
        $this->client = Client::create([
            'client_name' => 'Alice Customer',
            'phone_number' => '08111111111',
            'address' => 'Customer Address 1',
        ]);

        // Create product setup
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

        // Create Accounts (COA)
        $this->assetAccount = Account::create([
            'code' => '1001',
            'name' => 'Cash',
            'type' => 'asset',
            'subtype' => 'cash',
            'is_active' => true,
        ]);

        $this->categoryAccount = Account::create([
            'code' => '4001',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);
    }

    public function test_payment_input_auto_creates_cash_book_and_journal_entries(): void
    {
        $this->actingAs($this->user);

        // Create a transaction
        $transaction = new Transaction([
            'trx_id' => 'INV-TEST-001',
            'client_id' => $this->client->id,
            'total_price' => 500000.00,
            'total_discount' => 0.00,
            'grand_total' => 500000.00,
            'status' => 'waiting for payment',
        ]);
        $transaction->save();

        // Submit input payment
        $response = $this->post(route('transactions.payment', $transaction->id), [
            'payment_date' => '2026-06-05 10:00:00',
            'bank_name' => 'Bank BCA',
            'account_number' => '123456789',
            'amount' => 300000.00,
            'transfer_to_id' => $this->assetAccount->id,
            'category_id' => $this->categoryAccount->id,
        ]);

        $response->assertRedirect();
        
        // Verify payment is saved
        $this->assertDatabaseHas('transaction_payments', [
            'transaction_id' => $transaction->id,
            'amount' => 300000.00,
            'bank_name' => $this->assetAccount->name,
            'account_number' => '123456789',
        ]);

        // Verify CashTransaction is auto created
        $cashTx = CashTransaction::where('reference_type', 'transaction')
            ->where('reference_id', 'INV-TEST-001')
            ->first();
            
        $this->assertNotNull($cashTx);
        $this->assertEquals('money_in', $cashTx->type);
        $this->assertEquals(300000.00, (float)$cashTx->amount);
        $this->assertEquals($this->assetAccount->id, $cashTx->account_id);
        $this->assertEquals($this->categoryAccount->id, $cashTx->counter_account_id);
        $this->assertEquals($this->client->id, $cashTx->client_id);
        $this->assertEquals('Pembayaran transaksi - INV-TEST-001', $cashTx->description);

        // Verify Journal Entry is created for this CashTransaction
        $journal = JournalEntry::where('reference_type', 'cashbook')
            ->where('reference_id', $cashTx->id)
            ->first();
            
        $this->assertNotNull($journal);
        $this->assertEquals('Pembayaran transaksi - INV-TEST-001', $journal->description);

        // Verify Journal details: Debit to Asset account, Credit to Category account
        $details = $journal->details;
        $this->assertCount(2, $details);

        // Debit to cash (assetAccount)
        $debit = $details->where('account_id', $this->assetAccount->id)->first();
        $this->assertNotNull($debit);
        $this->assertEquals(300000.00, (float)$debit->debit);
        $this->assertEquals(0, (float)$debit->credit);

        // Credit to category (categoryAccount)
        $credit = $details->where('account_id', $this->categoryAccount->id)->first();
        $this->assertNotNull($credit);
        $this->assertEquals(0, (float)$credit->debit);
        $this->assertEquals(300000.00, (float)$credit->credit);
    }
}
