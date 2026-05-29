<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Account;
use App\Imports\CoaImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CoaImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'Accountant', 'is_active' => true]);
        $this->user = User::factory()->create(['role_id' => $this->role->id]);

        $permission = Permission::create([
            'menu_name' => 'C.O.A (Chart Of Accounts)',
            'route' => 'admin/coa',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($permission->id);
    }

    public function test_coa_import_can_process_collection_rows(): void
    {
        // 1. Create a parent account Cash first
        Account::create([
            'code' => '1000',
            'name' => 'Cash Group',
            'type' => 'asset',
            'is_active' => true,
        ]);

        // Define mock rows
        $rows = new Collection([
            [
                'code' => '1001',
                'name' => 'Cash in Hand',
                'type' => 'asset',
                'parent_account' => 'Cash Group', // Match parent by name
            ],
            [
                'code' => '1002',
                'name' => 'Bank Mandiri',
                'type' => 'asset',
                'parent_account' => '1000', // Match parent by code
            ],
            [
                'code' => '1000', // Existing code to test Update
                'name' => 'Cash Group Updated',
                'type' => 'asset',
                'parent_account' => '',
            ],
            [
                'code' => '4001',
                'name' => 'Sales Revenue',
                'type' => 'revenue',
                'parent_account' => '',
            ],
            [
                'code' => '9999',
                'name' => 'Invalid Type Account',
                'type' => 'invalid_type', // Invalid type
                'parent_account' => '',
            ],
            [
                'code' => '',
                'name' => 'Missing Code Account',
                'type' => 'asset',
                'parent_account' => '',
            ],
        ]);

        $importer = new CoaImport();
        $importer->collection($rows);

        // Assert counts
        $this->assertEquals(3, $importer->createdCount); // Cash in Hand, Bank Mandiri, Sales Revenue
        $this->assertEquals(1, $importer->updatedCount); // Cash Group Updated
        $this->assertEquals(2, $importer->skippedCount); // Invalid type and missing code

        // Assert database creations
        $this->assertDatabaseHas('accounts', [
            'code' => '1001',
            'name' => 'Cash in Hand',
            'type' => 'asset',
        ]);

        $this->assertDatabaseHas('accounts', [
            'code' => '1002',
            'name' => 'Bank Mandiri',
            'type' => 'asset',
        ]);

        $this->assertDatabaseHas('accounts', [
            'code' => '1000',
            'name' => 'Cash Group Updated',
        ]);

        // Check parent resolution
        $parent = Account::where('code', '1000')->first();
        $cashInHand = Account::where('code', '1001')->first();
        $bankMandiri = Account::where('code', '1002')->first();

        $this->assertEquals($parent->id, $cashInHand->parent_id);
        $this->assertEquals($parent->id, $bankMandiri->parent_id);

        // Verify skipped counts and error descriptions
        $this->assertCount(2, $importer->errors);
        $this->assertStringContainsString('Invalid account type', $importer->errors[0]);
        $this->assertStringContainsString('Code, Name, and Type are required', $importer->errors[1]);
    }

    public function test_coa_download_and_import_actions_respect_roles_permissions(): void
    {
        // 1. Without specific download & import permissions, these actions should NOT be visible
        $this->user->refresh();
        $this->user->unsetRelation('role');

        \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AccountResource\Pages\ManageAccounts::class)
            ->assertActionHidden('downloadTemplate')
            ->assertActionHidden('importCoa');

        // 2. Attach Import / Export permission
        $impExpPerm = Permission::create([
            'menu_name' => 'Import / Export',
            'route' => 'admin/import-export',
            'can_access' => true,
        ]);
        $this->role->permissions()->attach($impExpPerm->id);

        $this->user->refresh();
        $this->user->unsetRelation('role');

        // 3. Now the actions should be visible!
        \Livewire\Livewire::actingAs($this->user)
            ->test(\App\Filament\Resources\AccountResource\Pages\ManageAccounts::class)
            ->assertActionVisible('downloadTemplate')
            ->assertActionVisible('importCoa');
    }
}
