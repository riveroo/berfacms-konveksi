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
        Schema::create('account_monthly_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->integer('period_year');
            $table->integer('period_month');
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('debit_total', 15, 2)->default(0.00);
            $table->decimal('credit_total', 15, 2)->default(0.00);
            $table->decimal('closing_balance', 15, 2)->default(0.00);
            $table->boolean('is_dirty')->default(false);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['account_id', 'period_year', 'period_month'], 'amb_acct_yr_mo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_monthly_balances');
    }
};
