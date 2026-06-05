<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashBookTransferAndJournalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $cashAccount;
    protected Account $bankAccount;
    protected Account $expenseAccount;
    protected Account $revenueAccount;
    protected \App\Models\Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = \App\Models\Role::create([
            'name' => 'Finance Staff',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        $permission = \App\Models\Permission::create([
            'menu_name' => 'Cash Book',
            'route' => 'admin/cash-book',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Create accounts
        $this->cashAccount = Account::create([
            'code' => '1001',
            'name' => 'Cash',
            'type' => 'asset',
            'subtype' => 'cash',
            'is_active' => true,
        ]);

        $this->bankAccount = Account::create([
            'code' => '1002',
            'name' => 'Bank BCA',
            'type' => 'asset',
            'subtype' => 'bank',
            'is_active' => true,
        ]);

        $this->expenseAccount = Account::create([
            'code' => '5001',
            'name' => 'Electricity Expense',
            'type' => 'expense',
            'is_active' => true,
        ]);

        $this->revenueAccount = Account::create([
            'code' => '4001',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);
    }

    public function test_money_in_creates_balanced_journal_entries(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('cash-book.store'), [
            'date' => '2026-05-27',
            'description' => 'Receive customer payment',
            'type' => 'money_in',
            'amount' => 150000.00,
            'account_id' => $this->cashAccount->id,
            'counter_account_id' => $this->revenueAccount->id,
        ]);

        $response->assertRedirect(route('cash-book.index'));

        // Verify transaction saved
        $tx = CashTransaction::first();
        $this->assertNotNull($tx);
        $this->assertEquals('money_in', $tx->type);
        $this->assertEquals(150000.00, (float)$tx->amount);

        // Verify journal entry generated
        $journal = JournalEntry::where('reference_type', 'cashbook')
            ->where('reference_id', $tx->id)
            ->first();
        $this->assertNotNull($journal);
        $this->assertEquals('Receive customer payment', $journal->description);

        // Verify details
        $details = $journal->details;
        $this->assertCount(2, $details);

        // Debit: Cash account
        $debit = $details->where('account_id', $this->cashAccount->id)->first();
        $this->assertNotNull($debit);
        $this->assertEquals(150000.00, (float)$debit->debit);
        $this->assertEquals(0, (float)$debit->credit);

        // Credit: Revenue account
        $credit = $details->where('account_id', $this->revenueAccount->id)->first();
        $this->assertNotNull($credit);
        $this->assertEquals(0, (float)$credit->debit);
        $this->assertEquals(150000.00, (float)$credit->credit);
    }

    public function test_money_out_creates_balanced_journal_entries(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('cash-book.store'), [
            'date' => '2026-05-27',
            'description' => 'Pay electricity bill',
            'type' => 'money_out',
            'amount' => 85000.00,
            'account_id' => $this->cashAccount->id,
            'counter_account_id' => $this->expenseAccount->id,
        ]);

        $response->assertRedirect(route('cash-book.index'));

        $tx = CashTransaction::first();
        $this->assertNotNull($tx);
        $this->assertEquals('money_out', $tx->type);

        $journal = JournalEntry::where('reference_type', 'cashbook')
            ->where('reference_id', $tx->id)
            ->first();
        $this->assertNotNull($journal);

        $details = $journal->details;
        $this->assertCount(2, $details);

        // Credit: Cash account
        $credit = $details->where('account_id', $this->cashAccount->id)->first();
        $this->assertNotNull($credit);
        $this->assertEquals(0, (float)$credit->debit);
        $this->assertEquals(85000.00, (float)$credit->credit);

        // Debit: Expense account
        $debit = $details->where('account_id', $this->expenseAccount->id)->first();
        $this->assertNotNull($debit);
        $this->assertEquals(85000.00, (float)$debit->debit);
        $this->assertEquals(0, (float)$debit->credit);
    }

    public function test_transfer_creates_balanced_journal_entries(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('cash-book.store'), [
            'date' => '2026-05-27',
            'description' => 'Transfer Cash to Bank BCA',
            'type' => 'transfer',
            'amount' => 500000.00,
            'account_id' => $this->bankAccount->id, // destination
            'counter_account_id' => $this->cashAccount->id, // source
        ]);

        $response->assertRedirect(route('cash-book.index'));

        $tx = CashTransaction::first();
        $this->assertNotNull($tx);
        $this->assertEquals('transfer', $tx->type);

        $journal = JournalEntry::where('reference_type', 'cashbook')
            ->where('reference_id', $tx->id)
            ->first();
        $this->assertNotNull($journal);

        $details = $journal->details;
        $this->assertCount(2, $details);

        // Debit: Destination Account (Bank BCA)
        $debit = $details->where('account_id', $this->bankAccount->id)->first();
        $this->assertNotNull($debit);
        $this->assertEquals(500000.00, (float)$debit->debit);
        $this->assertEquals(0, (float)$debit->credit);

        // Credit: Source Account (Cash)
        $credit = $details->where('account_id', $this->cashAccount->id)->first();
        $this->assertNotNull($credit);
        $this->assertEquals(0, (float)$credit->debit);
        $this->assertEquals(500000.00, (float)$credit->credit);
    }

    public function test_transfer_validation_requires_different_accounts(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('cash-book.store'), [
            'date' => '2026-05-27',
            'description' => 'Invalid transfer',
            'type' => 'transfer',
            'amount' => 500000.00,
            'account_id' => $this->cashAccount->id,
            'counter_account_id' => $this->cashAccount->id, // Same account
        ]);

        $response->assertSessionHasErrors(['counter_account_id']);
        $this->assertDatabaseCount('cash_transactions', 0);
    }
}
