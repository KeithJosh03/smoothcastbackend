<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_media_images', function (Blueprint $table) {
            $table->id('product_img_id');
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');
            $table->string('url',225);
            $table->boolean('isMain');
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_media_images');
    }
};
