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

class JournalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Role $role;
    protected User $user;
    protected Account $cashAccount;
    protected Account $revenueAccount;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a role and user
        $this->role = Role::create([
            'name' => 'Accountant',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
            'is_active' => true,
        ]);

        // Create asset and revenue accounts
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

    public function test_guest_cannot_access_journal(): void
    {
        $response = $this->get('/admin/journal');
        $response->assertRedirect('/admin/login');
    }

    public function test_unauthorized_user_cannot_access_journal(): void
    {
        // No permission is attached to the role yet
        $response = $this->actingAs($this->user)->get('/admin/journal');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_journal(): void
    {
        // Grant permission
        $permission = Permission::create([
            'menu_name' => 'Journal',
            'route' => 'admin/journal',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        $response = $this->actingAs($this->user)->get('/admin/journal');
        $response->assertStatus(200);
        $response->assertViewIs('journal.index');
        $response->assertSee('General Journal');
    }

    public function test_journal_lists_records_filtered_by_month_and_year(): void
    {
        // Grant permission
        $permission = Permission::create([
            'menu_name' => 'Journal',
            'route' => 'admin/journal',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Entry 1: May 2026
        $entryMay = JournalEntry::create([
            'date' => '2026-05-15',
            'description' => 'May sales transaction',
            'reference_type' => 'test',
            'reference_id' => 1,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryMay->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 100000.00,
            'credit' => 0.00,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryMay->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 100000.00,
        ]);

        // Entry 2: June 2026
        $entryJune = JournalEntry::create([
            'date' => '2026-06-02',
            'description' => 'June sales transaction',
            'reference_type' => 'test',
            'reference_id' => 2,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryJune->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 250000.00,
            'credit' => 0.00,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entryJune->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 250000.00,
        ]);

        // Request for May 2026
        $response = $this->actingAs($this->user)->get('/admin/journal?filter_month=2026-05');
        $response->assertStatus(200);

        // Verify only May details are loaded
        $response->assertSee('May sales transaction');
        $response->assertSee('Rp 100.000');
        $response->assertDontSee('June sales transaction');
        $response->assertDontSee('Rp 250.000');
    }

    public function test_journal_export_pdf(): void
    {
        // Grant permission
        $permission = Permission::create([
            'menu_name' => 'Journal',
            'route' => 'admin/journal',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);

        // Create sample entry
        $entry = JournalEntry::create([
            'date' => '2026-05-20',
            'description' => 'General sales',
            'reference_type' => 'test',
            'reference_id' => 10,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->cashAccount->id,
            'debit' => 500000.00,
            'credit' => 0.00,
        ]);
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $this->revenueAccount->id,
            'debit' => 0.00,
            'credit' => 500000.00,
        ]);

        $response = $this->actingAs($this->user)->get('/admin/journal/export?filter_month=2026-05');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
