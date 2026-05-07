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
        // 1. Journal Entries
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->text('description');
                $table->string('reference_type')->nullable(); // e.g. sales, purchase, transaction
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
                
                $table->index(['reference_type', 'reference_id']);
            });
        }

        // 2. Journal Details
        if (!Schema::hasTable('journal_details')) {
            Schema::create('journal_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
                $table->foreignId('account_id')->constrained('accounts');
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->timestamps();

                $table->index('journal_entry_id');
                $table->index('account_id');
            });
        }

        // 3. Transaction Templates
        if (!Schema::hasTable('transaction_templates')) {
            Schema::create('transaction_templates', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->integer('version')->default(1);
                $table->timestamps();
            });
        }

        // 4. Transaction Template Lines
        if (!Schema::hasTable('transaction_template_lines')) {
            Schema::create('transaction_template_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('template_id')->constrained('transaction_templates')->onDelete('cascade');
                $table->foreignId('account_id')->constrained('accounts');
                $table->enum('position', ['debit', 'credit']);
                $table->enum('amount_source', ['input', 'formula', 'fixed']);
                $table->timestamps();

                $table->index('template_id');
                $table->index('account_id');
            });
        }

        // 5. Accounting Transactions (Business Level)
        if (!Schema::hasTable('accounting_transactions')) {
            Schema::create('accounting_transactions', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['sales', 'purchase', 'expense']);
                $table->date('date');
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_transactions');
        Schema::dropIfExists('transaction_template_lines');
        Schema::dropIfExists('transaction_templates');
        Schema::dropIfExists('journal_details');
        Schema::dropIfExists('journal_entries');
    }
};
