<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up any failed previous run
        DB::table('permissions')->where('route', 'admin/trial-balance')->delete();

        $id = DB::table('permissions')->insertGetId([
            'menu_name' => 'Trial Balance',
            'route' => 'admin/trial-balance',
            'can_access' => true,
        ]);

        // Also assign it to Super Admin role
        $superAdmin = DB::table('roles')->where('name', 'Super Admin')->first();
        if ($superAdmin) {
            DB::table('role_permissions')->where([
                'role_id' => $superAdmin->id,
                'permission_id' => $id,
            ])->delete();

            DB::table('role_permissions')->insert([
                'role_id' => $superAdmin->id,
                'permission_id' => $id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $perm = DB::table('permissions')->where('route', 'admin/trial-balance')->first();
        if ($perm) {
            DB::table('role_permissions')->where('permission_id', $perm->id)->delete();
            DB::table('permissions')->where('id', $perm->id)->delete();
        }
    }
};
