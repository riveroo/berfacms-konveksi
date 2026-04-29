<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('permission_role');
        
        \Illuminate\Support\Facades\DB::table('permissions')->delete();
        
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('menu_name');
            $table->boolean('can_read')->default(false);
            $table->boolean('can_add')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name')->unique();
            $table->dropColumn(['menu_name', 'can_read', 'can_add', 'can_edit', 'can_delete']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
        });
    }
};
