<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\BankTransfer;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankTransferModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $sourceAccount;
    protected Account $destinationAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create asset accounts
        $this->sourceAccount = Account::create([
            'code' => '1001',
            'name' => 'Cash in Hand',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $this->destinationAccount = Account::create([
            'code' => '1002',
            'name' => 'Bank Account',
            'type' => 'asset',
            'is_active' => true,
        ]);
    }

    public function test_creating_bank_transfer_automatically_creates_balanced_journal_entry(): void
    {
        $bankTransfer = BankTransfer::create([
            'date' => '2026-05-27',
            'from_account_id' => $this->sourceAccount->id,
            'to_account_id' => $this->destinationAccount->id,
            'amount' => 1500000.00,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('bank_transfers', [
            'id' => $bankTransfer->id,
            'amount' => 1500000.00,
        ]);

        // Verify Journal Entry exists
        $journal = JournalEntry::where('reference_type', 'bank_transfer')
            ->where('reference_id', $bankTransfer->id)
            ->first();
        $this->assertNotNull($journal);
        $this->assertEquals('2026-05-27', $journal->date->format('Y-m-d'));
        $this->assertEquals('Bank Transfer', $journal->description);

        // Verify details
        $details = $journal->details;
        $this->assertCount(2, $details);

        // DEBIT destination account (to_account_id)
        $debit = $details->where('account_id', $this->destinationAccount->id)->first();
        $this->assertNotNull($debit);
        $this->assertEquals(1500000.00, (float)$debit->debit);
        $this->assertEquals(0, (float)$debit->credit);

        // CREDIT source account (from_account_id)
        $credit = $details->where('account_id', $this->sourceAccount->id)->first();
        $this->assertNotNull($credit);
        $this->assertEquals(0, (float)$credit->debit);
        $this->assertEquals(1500000.00, (float)$credit->credit);
    }

    public function test_deleting_bank_transfer_deletes_journal_entry_and_details(): void
    {
        $bankTransfer = BankTransfer::create([
            'date' => '2026-05-27',
            'from_account_id' => $this->sourceAccount->id,
            'to_account_id' => $this->destinationAccount->id,
            'amount' => 2000000.00,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'bank_transfer',
            'reference_id' => $bankTransfer->id,
        ]);

        $bankTransfer->delete();

        // Journal and details should be cascade deleted
        $this->assertDatabaseMissing('journal_entries', [
            'reference_type' => 'bank_transfer',
            'reference_id' => $bankTransfer->id,
        ]);
    }

    public function test_cannot_transfer_to_the_same_account(): void
    {
        $this->expectException(\Exception::class);

        // Attempting to transfer from sourceAccount to sourceAccount should trigger model validation exception
        BankTransfer::create([
            'date' => '2026-05-27',
            'from_account_id' => $this->sourceAccount->id,
            'to_account_id' => $this->sourceAccount->id,
            'amount' => 500000.00,
            'user_id' => $this->user->id,
        ]);
    }
}
