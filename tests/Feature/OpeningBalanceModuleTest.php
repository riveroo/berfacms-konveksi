<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\OpeningBalance;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpeningBalanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $assetAccount;
    protected Account $equityAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create asset and equity accounts
        $this->assetAccount = Account::create([
            'code' => '1001',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $this->equityAccount = Account::create([
            'code' => '3001',
            'name' => 'Capital',
            'type' => 'equity',
            'is_active' => true,
        ]);
    }

    public function test_creating_opening_balance_automatically_creates_balanced_journal_entry(): void
    {
        $openingBalance = OpeningBalance::create([
            'date' => '2026-05-27',
            'account_id' => $this->assetAccount->id,
            'counter_account_id' => $this->equityAccount->id,
            'amount' => 5000000.00,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('opening_balances', [
            'id' => $openingBalance->id,
            'amount' => 5000000.00,
        ]);

        // Verify Journal Entry exists
        $journal = JournalEntry::where('reference_type', 'opening_balance')
            ->where('reference_id', $openingBalance->id)
            ->first();
        $this->assertNotNull($journal);
        $this->assertEquals('2026-05-27', $journal->date->format('Y-m-d'));
        $this->assertEquals('opening balance', $journal->description);

        // Verify details
        $details = $journal->details;
        $this->assertCount(2, $details);

        // DEBIT asset account
        $debit = $details->where('account_id', $this->assetAccount->id)->first();
        $this->assertNotNull($debit);
        $this->assertEquals(5000000.00, (float)$debit->debit);
        $this->assertEquals(0, (float)$debit->credit);

        // CREDIT counter account
        $credit = $details->where('account_id', $this->equityAccount->id)->first();
        $this->assertNotNull($credit);
        $this->assertEquals(0, (float)$credit->debit);
        $this->assertEquals(5000000.00, (float)$credit->credit);
    }

    public function test_updating_opening_balance_updates_journal_entry(): void
    {
        $openingBalance = OpeningBalance::create([
            'date' => '2026-05-27',
            'account_id' => $this->assetAccount->id,
            'counter_account_id' => $this->equityAccount->id,
            'amount' => 5000000.00,
            'user_id' => $this->user->id,
        ]);

        $openingBalance->update([
            'amount' => 7500000.00,
        ]);

        // Verify Journal Entry updated
        $journal = JournalEntry::where('reference_type', 'opening_balance')
            ->where('reference_id', $openingBalance->id)
            ->first();
        $this->assertNotNull($journal);

        $details = $journal->details;
        $this->assertCount(2, $details);

        $debit = $details->where('account_id', $this->assetAccount->id)->first();
        $this->assertEquals(7500000.00, (float)$debit->debit);

        $credit = $details->where('account_id', $this->equityAccount->id)->first();
        $this->assertEquals(7500000.00, (float)$credit->credit);
    }

    public function test_deleting_opening_balance_deletes_journal_entry(): void
    {
        $openingBalance = OpeningBalance::create([
            'date' => '2026-05-27',
            'account_id' => $this->assetAccount->id,
            'counter_account_id' => $this->equityAccount->id,
            'amount' => 5000000.00,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'opening_balance',
            'reference_id' => $openingBalance->id,
        ]);

        $openingBalance->delete();

        // Journal and details should be deleted
        $this->assertDatabaseMissing('journal_entries', [
            'reference_type' => 'opening_balance',
            'reference_id' => $openingBalance->id,
        ]);
    }

    public function test_cannot_create_duplicate_opening_balance_for_same_account(): void
    {
        OpeningBalance::create([
            'date' => '2026-05-27',
            'account_id' => $this->assetAccount->id,
            'counter_account_id' => $this->equityAccount->id,
            'amount' => 5000000.00,
            'user_id' => $this->user->id,
        ]);

        $this->expectException(\Exception::class);

        // Attempting to create duplicate for the same account
        OpeningBalance::create([
            'date' => '2026-05-27',
            'account_id' => $this->assetAccount->id,
            'counter_account_id' => $this->equityAccount->id,
            'amount' => 3000000.00,
            'user_id' => $this->user->id,
        ]);
    }
}
