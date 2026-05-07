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
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('type', ['customer', 'supplier', 'vendor', 'other'])->default('customer')->after('client_name');
            $table->string('email')->nullable()->after('type');
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null')->after('amount');
            $table->string('receive_from')->nullable()->after('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'receive_from']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['type', 'email']);
        });
    }
};
