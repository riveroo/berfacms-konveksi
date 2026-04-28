<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate first to avoid duplicates or old data issues when re-running
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menus = ['products', 'transactions', 'pre_orders', 'reports', 'inventory', 'users', 'roles'];

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);

        $adminPermissionIds = [];
        $staffPermissionIds = [];

        foreach ($menus as $menu) {
            // Admin gets full access
            $adminPerm = Permission::firstOrCreate([
                'menu_name' => $menu,
                'can_read' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
            $adminPermissionIds[] = $adminPerm->id;

            // Staff gets limited access
            $isStaffAccessible = in_array($menu, ['products', 'transactions', 'inventory']);
            $staffPerm = Permission::firstOrCreate([
                'menu_name' => $menu,
                'can_read' => $isStaffAccessible,
                'can_add' => $isStaffAccessible,
                'can_edit' => false,
                'can_delete' => false,
            ]);
            $staffPermissionIds[] = $staffPerm->id;
        }

        $adminRole->permissions()->sync($adminPermissionIds);
        $staffRole->permissions()->sync($staffPermissionIds);

        // Assign Admin role to the first user if exists, or create a default admin
        $adminUser = User::first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@konveksihub.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
        
        if (!$adminUser->role_id) {
            $adminUser->update(['role_id' => $adminRole->id]);
        }
    }
}
