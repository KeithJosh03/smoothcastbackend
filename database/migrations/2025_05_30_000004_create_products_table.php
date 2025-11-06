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
                ->onDelete('cascade')
                ->index();
            $table
                ->foreignId('sub_category_id')
                ->nullable()
                ->constrained('sub_categories', 'sub_category_id')
                ->onDelete('cascade')
                ->index();;
            $table
                ->foreignId('brand_id')
                ->nullable()
                ->constrained('brands','brand_id')
                ->onDelete('cascade')
                ->index();;
            $table->string('product_name', 100)->unique();
            $table->decimal('base_price',10,2);
            $table->text('description')
                ->nullable();
            $table->timestamp('release')
                ->useCurrent()
                ->nullable();
            $table->text('features')
                ->nullable();
            $table->text('specifications')
                ->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
