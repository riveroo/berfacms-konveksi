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
        // Clear all previous data because structure changes completely
        DB::table('role_permissions')->delete();
        DB::table('permissions')->delete();

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['can_read', 'can_add', 'can_edit', 'can_delete']);
            $table->string('route')->after('menu_name')->nullable();
            $table->boolean('can_access')->default(false)->after('route');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['route', 'can_access']);
            $table->boolean('can_read')->default(false);
            $table->boolean('can_add')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
        });
    }
};
