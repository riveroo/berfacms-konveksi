<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AccountMonthlyBalance;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use App\Services\Accounting\MonthlyBalanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyBalanceSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected Account $cashAccount;
    protected Account $revenueAccount;
    protected MonthlyBalanceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MonthlyBalanceService::class);

        $this->cashAccount = Account::create([
            'code' => '1001',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $this->revenueAccount = Account::create([
            'code' => '4001',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);

        $user = \App\Models\User::factory()->create(['id' => 1]);

        \App\Models\OpeningBalance::create([
            'date' => '2026-01-10',
            'account_id' => $this->cashAccount->id,
            'counter_account_id' => $this->revenueAccount->id,
            'amount' => 1000.00,
            'user_id' => $user->id,
        ]);
    }

    public function test_sequential_chain_generation_works_correctly(): void
    {
        // 1. The OpeningBalance created in setUp already generates a January 2026 journal entry with 1000.00

        // 2. Create another journal entry in Mar 2026
        $entryMar = JournalEntry::create(['date' => '2026-03-05', 'description' => 'Sales']);
        JournalDetail::create([
            'journal_entry_id' => $entryMar->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 500.00,
            'credit' => 0.00,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryMar->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 500.00,
        ]);

        // Trigger ensureSnapshotsUpTo for April 2026
        $this->service->ensureSnapshotsUpTo(2026, 4);

        // Verify that snapshots exist for Jan, Feb, Mar, and Apr 2026
        $this->assertDatabaseHas('account_monthly_balances', [
            'account_id' => $this->cashAccount->id,
            'period_year' => 2026,
            'period_month' => 1,
            'opening_balance' => 0.00,
            'debit_total' => 1000.00,
            'credit_total' => 0.00,
            'closing_balance' => 1000.00,
            'is_dirty' => false,
        ]);

        $this->assertDatabaseHas('account_monthly_balances', [
            'account_id' => $this->cashAccount->id,
            'period_year' => 2026,
            'period_month' => 2,
            'opening_balance' => 1000.00,
            'debit_total' => 0.00,
            'credit_total' => 0.00,
            'closing_balance' => 1000.00,
            'is_dirty' => false,
        ]);

        $this->assertDatabaseHas('account_monthly_balances', [
            'account_id' => $this->cashAccount->id,
            'period_year' => 2026,
            'period_month' => 3,
            'opening_balance' => 1000.00,
            'debit_total' => 500.00,
            'credit_total' => 0.00,
            'closing_balance' => 1500.00,
            'is_dirty' => false,
        ]);

        $this->assertDatabaseHas('account_monthly_balances', [
            'account_id' => $this->cashAccount->id,
            'period_year' => 2026,
            'period_month' => 4,
            'opening_balance' => 1500.00,
            'debit_total' => 0.00,
            'credit_total' => 0.00,
            'closing_balance' => 1500.00,
            'is_dirty' => false,
        ]);
    }

    public function test_backdated_journal_marks_snapshots_as_dirty(): void
    {
        // 1. Generate clean snapshots up to March 2026

        $this->service->ensureSnapshotsUpTo(2026, 3);

        // Verify none are dirty
        $this->assertEquals(0, AccountMonthlyBalance::where('is_dirty', true)->count());

        // 2. Perform backdated journal insertion in Feb 2026
        $entryFeb = JournalEntry::create(['date' => '2026-02-15', 'description' => 'Backdated sale']);
        JournalDetail::create([
            'journal_entry_id' => $entryFeb->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 200.00,
            'credit' => 0.00,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryFeb->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 200.00,
        ]);

        // Verify that Feb and Mar snapshots are now dirty (Jan is NOT dirty since transaction is in Feb)
        $this->assertDatabaseHas('account_monthly_balances', [
            'period_year' => 2026,
            'period_month' => 1,
            'is_dirty' => false,
        ]);

        $this->assertDatabaseHas('account_monthly_balances', [
            'period_year' => 2026,
            'period_month' => 2,
            'is_dirty' => true,
        ]);

        $this->assertDatabaseHas('account_monthly_balances', [
            'period_year' => 2026,
            'period_month' => 3,
            'is_dirty' => true,
        ]);

        // 3. Triggering ensureSnapshotsUpTo again should regenerate Feb and Mar to clean state
        $this->service->ensureSnapshotsUpTo(2026, 3);

        $this->assertDatabaseHas('account_monthly_balances', [
            'account_id' => $this->cashAccount->id,
            'period_year' => 2026,
            'period_month' => 2,
            'opening_balance' => 1000.00,
            'debit_total' => 200.00,
            'closing_balance' => 1200.00,
            'is_dirty' => false,
        ]);

        $this->assertDatabaseHas('account_monthly_balances', [
            'account_id' => $this->cashAccount->id,
            'period_year' => 2026,
            'period_month' => 3,
            'opening_balance' => 1200.00,
            'debit_total' => 0.00,
            'closing_balance' => 1200.00,
            'is_dirty' => false,
        ]);
    }
}
