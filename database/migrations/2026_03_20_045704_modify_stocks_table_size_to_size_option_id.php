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
        // 1. Create default size options and any existing distinct sizes from stocks table
        $sizes = \Illuminate\Support\Facades\DB::table('stocks')->select('size')->distinct()->pluck('size');
        foreach ($sizes as $size) {
            if ($size) {
                \Illuminate\Support\Facades\DB::table('size_options')->insertOrIgnore(['name' => $size, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        foreach (['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $s) {
            \Illuminate\Support\Facades\DB::table('size_options')->insertOrIgnore(['name' => $s, 'created_at' => now(), 'updated_at' => now()]);
        }

        // 2. Add size_option_id column to stocks
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('size_option_id')->nullable()->constrained('size_options')->nullOnDelete();
        });

        // 3. Migrate data
        $stocks = \Illuminate\Support\Facades\DB::table('stocks')->get();
        foreach ($stocks as $stock) {
            if ($stock->size) {
                $sizeOption = \Illuminate\Support\Facades\DB::table('size_options')->where('name', $stock->size)->first();
                if ($sizeOption) {
                    \Illuminate\Support\Facades\DB::table('stocks')->where('id', $stock->id)->update(['size_option_id' => $sizeOption->id]);
                }
            }
        }

        // 4. Drop old size column
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('size')->nullable();
        });

        $stocks = \Illuminate\Support\Facades\DB::table('stocks')->get();
        foreach ($stocks as $stock) {
            if ($stock->size_option_id) {
                $sizeOption = \Illuminate\Support\Facades\DB::table('size_options')->where('id', $stock->size_option_id)->first();
                if ($sizeOption) {
                    \Illuminate\Support\Facades\DB::table('stocks')->where('id', $stock->id)->update(['size' => $sizeOption->name]);
                }
            }
        }

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['size_option_id']);
            $table->dropColumn('size_option_id');
        });
    }
};
