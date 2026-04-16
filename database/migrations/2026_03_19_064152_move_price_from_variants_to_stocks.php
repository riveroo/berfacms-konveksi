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
        if (Schema::hasColumn('variants', 'price')) {
            Schema::table('variants', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }

        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        
        Schema::table('variants', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('variant_name');
        });
    }
};
