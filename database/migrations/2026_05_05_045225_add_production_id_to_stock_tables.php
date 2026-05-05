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
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->foreignId('production_id')->nullable()->after('id')->constrained('productions')->onDelete('set null');
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->foreignId('production_id')->nullable()->after('id')->constrained('productions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('production_id');
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('production_id');
        });
    }
};
