<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Main Promotions Table
        Schema::create('promotions', function (Blueprint $table) {
            $table->id('promotion_id');
            $table->string('name');
            $table->enum('discount_type', ['PERCENTAGE', 'FIXED_AMOUNT']);
            $table->decimal('discount_value', 10, 2);
            $table->enum('apply_to', ['ALL', 'CATEGORY', 'PRODUCT'])->default('ALL');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Pivot Table for Category-level Promotions
        Schema::create('promotion_category', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained('promotions', 'promotion_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->primary(['promotion_id', 'category_id']);
        });

        // 3. Pivot Table for Product-level Promotions
        Schema::create('promotion_product', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained('promotions', 'promotion_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->primary(['promotion_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_product');
        Schema::dropIfExists('promotion_category');
        Schema::dropIfExists('promotions');
    }
};