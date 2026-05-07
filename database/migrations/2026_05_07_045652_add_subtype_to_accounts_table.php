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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('subtype')->default('general')->after('type');
        });

        // Seed basic subtypes for existing accounts based on AccountSeeder logic
        DB::table('accounts')->where('code', '1001')->update(['subtype' => 'cash']);
        DB::table('accounts')->where('code', '1002')->update(['subtype' => 'bank']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('subtype');
        });
    }
};
