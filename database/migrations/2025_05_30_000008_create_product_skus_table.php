<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id('sku_id');

            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->cascadeOnDelete();

            $table->string('sku_code', 64)->unique();
            $table->decimal('added_price', 10, 2)->default(0.00);
            $table->unsignedInteger('stock_quantity')->default(0);

            $table->timestamps();

            $table->index(
                ['product_id', 'stock_quantity'],
                'product_skus_product_stock_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
};
