<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Filament\Pages\GeneralLedger;

class GeneralLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Account $cashAccount;
    protected Account $revenueAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create([
            'name' => 'Accountant',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

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
    }

    public function test_guest_cannot_access_general_ledger(): void
    {
        $response = $this->get('/admin/general-ledger');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_general_ledger(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/general-ledger');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_general_ledger(): void
    {
        $permission = Permission::create([
            'menu_name' => 'General Ledger',
            'route' => 'admin/general-ledger',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/general-ledger');
        $response->assertStatus(200);
    }

    public function test_general_ledger_calculates_running_balance_correctly(): void
    {
        $permission = Permission::create([
            'menu_name' => 'General Ledger',
            'route' => 'admin/general-ledger',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // 1. Setup transactions for Cash (Asset - normal debit balance)
        $entry1 = JournalEntry::create(['date' => '2026-05-01', 'description' => 'Opening Cash']);
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 1000.00,
            'credit' => 0.00,
        ]);

        $entry2 = JournalEntry::create(['date' => '2026-05-03', 'description' => 'Payment out']);
        JournalDetail::create([
            'journal_entry_id' => $entry2->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 0.00,
            'credit' => 300.00,
        ]);

        $entry3 = JournalEntry::create(['date' => '2026-05-05', 'description' => 'Cash received']);
        JournalDetail::create([
            'journal_entry_id' => $entry3->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 500.00,
            'credit' => 0.00,
        ]);

        // Test with Livewire page component
        Livewire::actingAs($this->user)
            ->test(GeneralLedger::class)
            ->set('accountId', $this->cashAccount->id)
            ->set('period', '2026-05')
            ->assertSet('totalDebitValue', 1500.00)
            ->assertSet('totalCreditValue', 300.00)
            ->assertSet('endingBalanceValue', 1200.00)
            ->assertViewHas('balancesCache', function ($cache) {
                // Assert running balances: Row 0 = 0.00 (Opening Balance), Row 1 = 1000, Row 2 = 700, Row 3 = 1200
                $balances = array_values($cache);
                return $balances[0] == 0.00 && $balances[1] == 1000.00 && $balances[2] == 700.00 && $balances[3] == 1200.00;
            });
    }

    public function test_general_ledger_excel_and_pdf_exports(): void
    {
        $permission = Permission::create([
            'menu_name' => 'General Ledger',
            'route' => 'admin/general-ledger',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $entry = JournalEntry::create(['date' => '2026-05-01', 'description' => 'Opening Cash']);
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 1000.00,
            'credit' => 0.00,
        ]);

        $responseExcel = $this->actingAs($this->user)->get('/admin/general-ledger/export-excel?account_id=' . $this->cashAccount->id . '&period=2026-05');
        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responsePdf = $this->actingAs($this->user)->get('/admin/general-ledger/export-pdf?account_id=' . $this->cashAccount->id . '&period=2026-05');
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');
    }
}
