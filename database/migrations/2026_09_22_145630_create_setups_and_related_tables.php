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
        Schema::dropIfExists('inclusions');
        Schema::dropIfExists('setup_items');
        Schema::dropIfExists('setups');

        Schema::create('setups', function (Blueprint $table) {
            $table->id('setup_id');
            $table->string('bundle_title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->enum('pricing_type', ['fixed', 'calculated'])->default('fixed');
            $table->decimal('bundle_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_published')->default(false);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('setup_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setup_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sku_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->boolean('is_required')->default(true);

            $table->foreign('setup_id')->references('setup_id')->on('setups')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('sku_id')->references('sku_id')->on('product_skus')->onDelete('set null');
        });

        Schema::create('inclusions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setup_id');
            $table->string('title');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->boolean('is_required')->default(true);

            $table->foreign('setup_id')->references('setup_id')->on('setups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inclusions');
        Schema::dropIfExists('setup_items');
        Schema::dropIfExists('setups');
    }
};
