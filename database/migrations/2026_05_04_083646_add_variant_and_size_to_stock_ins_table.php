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
            $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('variants')->onDelete('set null');
            $table->foreignId('size_option_id')->nullable()->after('variant_id')->constrained('size_options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropForeign(['size_option_id']);
            $table->dropColumn(['variant_id', 'size_option_id']);
        });
    }
};
