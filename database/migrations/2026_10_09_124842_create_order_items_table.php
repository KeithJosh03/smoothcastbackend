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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            
            // References
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();
            
            $table->unsignedBigInteger('sku_id')->nullable();
            $table->foreign('sku_id')->references('sku_id')->on('product_skus')->nullOnDelete();
            
            $table->unsignedBigInteger('setup_id')->nullable();
            $table->foreign('setup_id')->references('setup_id')->on('setups')->nullOnDelete();

            // Snapshots
            $table->string('title_snapshot');
            $table->decimal('price_snapshot', 10, 2);
            $table->integer('weight_snapshot')->default(0);
            $table->integer('quantity');
            $table->decimal('discount_applied_snapshot', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
