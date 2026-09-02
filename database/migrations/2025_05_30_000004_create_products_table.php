<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('brand_id')->constrained('brands', 'brand_id');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->foreignId('sub_category_id')->constrained('sub_categories', 'sub_category_id');
            $table->string('product_title');
            $table->decimal('base_price', 10, 2);
            $table->text('description');
            $table->text('features')->nullable();
            $table->text('specifications')->nullable();
            $table->date('release_date')->nullable();
            
            // 🚨 ADD THESE FOR NO-VARIANT SUPPORT
            $table->string('sku')->nullable()->unique();
            $table->integer('stock_quantity')->nullable()->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
