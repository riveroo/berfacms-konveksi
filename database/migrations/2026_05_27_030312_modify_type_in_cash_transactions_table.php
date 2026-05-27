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
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });

        // Migrate existing types
        DB::table('cash_transactions')->where('type', 'in')->update(['type' => 'money_in']);
        DB::table('cash_transactions')->where('type', 'out')->update(['type' => 'money_out']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert types first
        DB::table('cash_transactions')->where('type', 'money_in')->update(['type' => 'in']);
        DB::table('cash_transactions')->where('type', 'money_out')->update(['type' => 'out']);

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->enum('type', ['in', 'out'])->change();
        });
    }
};
