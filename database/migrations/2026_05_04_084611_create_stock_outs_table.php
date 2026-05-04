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
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('trx_date');
            $table->enum('item_type', ['product', 'material']);
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('variant_id')->nullable()->constrained('variants')->onDelete('set null');
            $table->foreignId('size_option_id')->nullable()->constrained('size_options')->onDelete('set null');
            $table->string('item_name')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('set null');
            $table->integer('quantity');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
