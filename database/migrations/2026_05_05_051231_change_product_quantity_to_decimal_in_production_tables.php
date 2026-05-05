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
        Schema::table('production_products', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->change();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('stock', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_products', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->integer('stock')->change();
        });
    }
};
