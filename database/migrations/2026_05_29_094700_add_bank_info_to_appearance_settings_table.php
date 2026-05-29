<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appearance_settings', function (Blueprint $table) {
            $table->string('bank_logo')->nullable()->after('favicon');
            $table->string('bank_account_number')->nullable()->after('bank_logo');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appearance_settings', function (Blueprint $table) {
            $table->dropColumn(['bank_logo', 'bank_account_number', 'bank_account_name']);
        });
    }
};
