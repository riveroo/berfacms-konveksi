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

class ProfitLossModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Account $salesRevenueAccount;
    protected Account $salaryExpenseAccount;

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

        // Create revenue and expense accounts
        $this->salesRevenueAccount = Account::create([
            'code' => '4001',
            'name' => 'Sales Revenue',
            'type' => 'revenue',
            'is_active' => true,
        ]);

        $this->salaryExpenseAccount = Account::create([
            'code' => '5001',
            'name' => 'Salary Expense',
            'type' => 'expense',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_profit_loss(): void
    {
        $response = $this->get('/admin/reports/profit-loss');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_profit_loss(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/reports/profit-loss');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_profit_loss(): void
    {
        // Grant permissions
        $permission = Permission::create([
            'menu_name' => 'Profit & Loss',
            'route' => 'admin/reports/profit-loss',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/reports/profit-loss');
        $response->assertStatus(200);
        $response->assertSee(__('finance.profit_loss_statement'));
    }

    public function test_profit_loss_calculates_balances_using_debit_credit_rules(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Profit & Loss',
            'route' => 'admin/reports/profit-loss',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Create sample journal entries for May 2026
        $entry1 = JournalEntry::create([
            'date' => '2026-05-10',
            'description' => 'Sales transaction',
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entry1->id,
            'account_id' => $this->salesRevenueAccount->id,
            'debit' => 0.00,
            'credit' => 2000000.00, // Revenue Normal Credit balance
        ]);

        $entry2 = JournalEntry::create([
            'date' => '2026-05-25',
            'description' => 'Salary payment',
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entry2->id,
            'account_id' => $this->salaryExpenseAccount->id,
            'debit' => 500000.00, // Expense Normal Debit balance
            'credit' => 0.00,
        ]);

        $response = $this->actingAs($this->user)->get('/admin/reports/profit-loss?filter_month=2026-05');
        $response->assertStatus(200);

        // Verify Income (Revenue) total = 2,000,000
        $response->assertSee('Rp 2.000.000');
        // Verify Expenses total = 500,000
        $response->assertSee('Rp 500.000');
        // Verify Net Profit = 1,500,000
        $response->assertSee('Rp 1.500.000');
    }

    public function test_profit_loss_filtering_by_month_and_year(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Profit & Loss',
            'route' => 'admin/reports/profit-loss',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Entry in May 2026
        $entryMay = JournalEntry::create([
            'date' => '2026-05-15',
            'description' => 'May sale',
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryMay->id,
            'account_id' => $this->salesRevenueAccount->id,
            'debit' => 0.00,
            'credit' => 3000000.00,
        ]);

        // Entry in June 2026
        $entryJune = JournalEntry::create([
            'date' => '2026-06-12',
            'description' => 'June sale',
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryJune->id,
            'account_id' => $this->salesRevenueAccount->id,
            'debit' => 0.00,
            'credit' => 4500000.00,
        ]);

        // Query only May
        $responseMay = $this->actingAs($this->user)->get('/admin/reports/profit-loss?filter_type=monthly&filter_month=2026-05');
        $responseMay->assertSee('Rp 3.000.000');
        $responseMay->assertDontSee('Rp 4.500.000');

        // Query all of year 2026
        $responseYear = $this->actingAs($this->user)->get('/admin/reports/profit-loss?filter_type=yearly&filter_year=2026');
        // Total should be 7,500,000
        $responseYear->assertSee('Rp 7.500.000');
    }

    public function test_profit_loss_drilldown_api(): void
    {
        // Grant permissions for index and drilldown
        $permissionIndex = Permission::create([
            'menu_name' => 'Profit & Loss',
            'route' => 'admin/reports/profit-loss',
            'can_access' => true,
        ]);
        $permissionDrilldown = Permission::create([
            'menu_name' => 'Profit & Loss Drilldown',
            'route' => 'admin/reports/profit-loss/drilldown',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach([$permissionIndex->id, $permissionDrilldown->id]);

        $entry = JournalEntry::create([
            'date' => '2026-05-10',
            'description' => 'Consulting services invoice',
            'reference_type' => 'sales',
            'reference_id' => 42,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->salesRevenueAccount->id,
            'debit' => 0.00,
            'credit' => 1250000.00,
        ]);

        $response = $this->actingAs($this->user)->get('/admin/reports/profit-loss/drilldown?account_id=' . $this->salesRevenueAccount->id . '&filter_month=2026-05');
        $response->assertStatus(200);
        $response->assertJsonPath('account_name', 'Sales Revenue');
        $response->assertJsonFragment([
            'date' => '10/05/2026',
            'description' => 'Consulting services invoice',
            'reference' => 'Sales #42',
            'amount' => 'Rp 1.250.000',
        ]);
    }

    public function test_profit_loss_pdf_and_excel_export(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Profit & Loss',
            'route' => 'admin/reports/profit-loss',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // PDF download response check
        $responsePdf = $this->actingAs($this->user)->get('/admin/reports/profit-loss/export-pdf?filter_month=2026-05');
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');

        // Excel download response check
        $responseExcel = $this->actingAs($this->user)->get('/admin/reports/profit-loss/export-excel?filter_month=2026-05');
        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
