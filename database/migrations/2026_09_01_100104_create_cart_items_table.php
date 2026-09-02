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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');

            // Foreign Key to carts table (cascade delete removes items if cart is cleared/deleted)
            $table->foreignId('cart_id')
                  ->constrained('carts', 'cart_id')
                  ->cascadeOnDelete();

            // Foreign Key to products table
            $table->foreignId('product_id')
                  ->constrained('products', 'product_id')
                  ->cascadeOnDelete();

            // Foreign Key to product_skus table (nullable for simple products without variants)
            $table->foreignId('sku_id')
                  ->nullable()
                  ->constrained('product_skus', 'sku_id')
                  ->nullOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            // Indexing for faster lookups when checking cart contents
            $table->index(['cart_id', 'product_id', 'sku_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};