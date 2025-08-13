<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table
                ->foreignId('category_id')
                ->constrained('categories', 'category_id')
                ->onDelete('cascade');
            $table
                ->foreignId('type_id')
                ->nullable()
                ->constrained('category_types', 'type_id')
                ->onDelete('cascade');
            $table
                ->foreignId('brand_id')
                ->nullable()
                ->constrained('brands','brand_id')
                ->onDelete('cascade');
            $table->string('product_name', 100);
            $table->decimal('base_price',10,2);
            $table->text('description')
                ->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
