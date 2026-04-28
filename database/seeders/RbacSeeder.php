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

        $menus = \App\Filament\Resources\RolePermissionResource::getMenusConfig();

        $adminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['is_active' => true]);
        $staffRole = Role::firstOrCreate(['name' => 'Staff'], ['is_active' => true]);

        $adminPermissionIds = [];
        $staffPermissionIds = [];

        foreach ($menus as $group => $items) {
            foreach ($items as $item) {
                // Admin gets full access
                $adminPerm = Permission::firstOrCreate([
                    'menu_name' => $item['name'],
                    'route' => $item['route'],
                    'can_access' => true,
                ]);
                $adminPermissionIds[] = $adminPerm->id;

                // Staff gets limited access
                $isStaffAccessible = in_array($item['name'], ['Products', 'Product Inventory', 'Orders', 'Pre Order']);
                if ($isStaffAccessible) {
                    $staffPerm = Permission::firstOrCreate([
                        'menu_name' => $item['name'],
                        'route' => $item['route'],
                        'can_access' => true,
                    ]);
                    $staffPermissionIds[] = $staffPerm->id;
                }
            }
        }

        $adminRole->permissions()->sync($adminPermissionIds);
        $staffRole->permissions()->sync($staffPermissionIds);

        // Assign Admin role to the admin user
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
        
        $adminUser->update(['role_id' => $adminRole->id]);
    }
}
