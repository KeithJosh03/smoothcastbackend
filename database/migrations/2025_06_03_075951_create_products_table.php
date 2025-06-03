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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_ID');
            $table->unsignedBigInteger('brand_ID')->nullable();
            $table->unsignedBigInteger('variant_ID');
            $table->unsignedBigInteger('producttpye_ID');
            $table->string('productName',100);
            $table->string('productDescript',255)->nullable();
            $table->string('price',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
