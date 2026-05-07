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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description')->nullable();
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 15, 2);
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('counter_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->string('reference_type')->nullable(); // manual, pos, invoice, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index('account_id');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
