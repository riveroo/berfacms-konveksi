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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('transaction_type', ['pre_order', 'direct_order'])->default('direct_order');
            $table->enum('item_status', ['in_progress', 'awaiting_pickup', 'collected'])->default('in_progress');
            $table->enum('payment_status', ['unpaid', 'deposit', 'paid'])->default('unpaid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'item_status', 'payment_status']);
        });
    }
};
