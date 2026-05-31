<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@berfaerp.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('DE1wm972L4DWapqk'),
                'role_id' => $role->id,
                'is_active' => true,
            ]
        );
    }
}
