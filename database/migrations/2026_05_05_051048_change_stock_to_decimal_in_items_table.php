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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('stock', 15, 2)->default(0)->change();
        });

        // Also update stock_ins and stock_outs quantity to decimal if they are currently integer
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('stock')->default(0)->change();
        });

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
