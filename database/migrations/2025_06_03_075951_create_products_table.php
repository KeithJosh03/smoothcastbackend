<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

     public function up(): void {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id('product_ID');
                $table->string('productName', 100);
                $table->decimal('price', 10, 2);
                $table->string('description', 255)->nullable();

                $table->foreignId('brand_ID')
                    ->nullable()
                    ->constrained('brands', 'brand_ID')
                    ->onDelete('cascade');

                $table->foreignId('category_ID')
                    ->constrained('categories', 'category_ID');
            });
        }
    }

    
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
