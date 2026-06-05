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
use App\Filament\Pages\TrialBalance;

class TrialBalanceTest extends TestCase
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

    public function test_guest_cannot_access_trial_balance(): void
    {
        $response = $this->get('/admin/trial-balance');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_trial_balance(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/trial-balance');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_trial_balance(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Trial Balance',
            'route' => 'admin/trial-balance',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/trial-balance');
        $response->assertStatus(200);
    }

    public function test_trial_balance_calculates_correctly(): void
    {
        $permission = Permission::create([
            'menu_name' => 'Trial Balance',
            'route' => 'admin/trial-balance',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Create transaction in May 2026
        $entry = JournalEntry::create(['date' => '2026-05-10', 'description' => 'Test Sales']);
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 1500.00,
            'credit' => 0.00,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 1500.00,
        ]);

        // Test with Livewire page component
        Livewire::actingAs($this->user)
            ->test(TrialBalance::class)
            ->set('period', '2026-05')
            ->assertViewHas('totalDebit', 1500.00)
            ->assertViewHas('totalCredit', 1500.00)
            ->assertViewHas('isBalanced', true)
            ->assertViewHas('rows', function ($rows) {
                // Cash (Asset): debit should be 1500.00
                $cashRow = collect($rows)->firstWhere('code', '1001');
                // Sales Revenue (Revenue): credit should be 1500.00
                $revenueRow = collect($rows)->firstWhere('code', '4001');
                
                return $cashRow['debit'] == 1500.00 && $revenueRow['credit'] == 1500.00;
            });
    }

    public function test_trial_balance_export_permission_blocks_unauthorized(): void
    {
        $permissionPage = Permission::create([
            'menu_name' => 'Trial Balance',
            'route' => 'admin/trial-balance',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permissionPage->id);

        // Attempting to export without 'admin/import-export' permission
        $responseExcel = $this->actingAs($this->user)->get('/admin/trial-balance/export-excel?period=2026-05');
        $responseExcel->assertStatus(403);

        $responsePdf = $this->actingAs($this->user)->get('/admin/trial-balance/export-pdf?period=2026-05');
        $responsePdf->assertStatus(403);
    }

    public function test_trial_balance_export_permission_allows_authorized(): void
    {
        $permissionPage = Permission::create([
            'menu_name' => 'Trial Balance',
            'route' => 'admin/trial-balance',
            'can_access' => true,
        ]);
        $permissionExport = Permission::create([
            'menu_name' => 'Import / Export',
            'route' => 'admin/import-export',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach([$permissionPage->id, $permissionExport->id]);

        $responseExcel = $this->actingAs($this->user)->get('/admin/trial-balance/export-excel?period=2026-05');
        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responsePdf = $this->actingAs($this->user)->get('/admin/trial-balance/export-pdf?period=2026-05');
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');
    }
}
