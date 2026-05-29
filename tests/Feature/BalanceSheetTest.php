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
use App\Filament\Pages\BalanceSheet;

class BalanceSheetTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Account $cashAccount;
    protected Account $payableAccount;
    protected Account $capitalAccount;
    protected Account $revenueAccount;
    protected Account $expenseAccount;

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

        // Create standard COA accounts
        $this->cashAccount = Account::create([
            'code' => '1001',
            'name' => 'Cash',
            'type' => 'asset',
            'is_active' => true,
        ]);

        $this->payableAccount = Account::create([
            'code' => '2001',
            'name' => 'Accounts Payable',
            'type' => 'liability',
            'is_active' => true,
        ]);

        $this->capitalAccount = Account::create([
            'code' => '3001',
            'name' => 'Owner Capital',
            'type' => 'equity',
            'is_active' => true,
        ]);

        $this->revenueAccount = Account::create([
            'code' => '4001',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);

        $this->expenseAccount = Account::create([
            'code' => '5001',
            'name' => 'Cost of Goods Sold',
            'type' => 'expense',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_balance_sheet(): void
    {
        $response = $this->get('/admin/balance-sheet');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_balance_sheet(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/balance-sheet');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_balance_sheet(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Balance Sheet',
            'route' => 'admin/balance-sheet',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/balance-sheet');
        $response->assertStatus(200);
    }

    public function test_balance_sheet_calculates_balances_correctly(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Balance Sheet',
            'route' => 'admin/balance-sheet',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Setup Transactions:
        // Cash debit = 1000
        $entry1 = JournalEntry::create(['date' => '2026-05-01', 'description' => 'Capital injection']);
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 1000.00,
            'credit' => 0.00,
        ]);
        // Owner Capital credit = 500
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->capitalAccount->id,
            'debit' => 0.00,
            'credit' => 500.00,
        ]);
        // Accounts Payable credit = 400
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->payableAccount->id,
            'debit' => 0.00,
            'credit' => 400.00,
        ]);
        // Sales Revenue credit = 200
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 200.00,
        ]);
        // Cost of Goods Sold debit = 100
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->expenseAccount->id,
            'debit' => 100.00,
            'credit' => 0.00,
        ]);

        // Retained Earnings = Revenue (200) - Expense (100) = 100.00
        // Total Assets = Cash (1000) = 1000.00
        // Total Liabilities & Equity = AP (400) + Share Capital (500) + Retained Earnings (100) = 1000.00
        // Balance = Balanced (isBalanced = true)

        Livewire::actingAs($this->user)
            ->test(BalanceSheet::class)
            ->set('period', '2026-05')
            ->assertViewHas('totalAssets', 1000.00)
            ->assertViewHas('totalLiabilitiesAndEquity', 1000.00)
            ->assertViewHas('retainedEarnings', 1000.00 - 900.00) // 100.00
            ->assertViewHas('isBalanced', true);
    }

    public function test_balance_sheet_exports(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Balance Sheet',
            'route' => 'admin/balance-sheet',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $responseExcel = $this->actingAs($this->user)->get('/admin/balance-sheet/export-excel?period=2026-05');
        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responsePdf = $this->actingAs($this->user)->get('/admin/balance-sheet/export-pdf?period=2026-05');
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');
    }
}
