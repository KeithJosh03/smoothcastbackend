<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('variant_id');
            $table
                ->foreignId('product_id')
                ->constrained('products','product_id')
                ->onDelete('cascade');
            $table->string('full_model_name',200);
            $table->decimal('product_price',10,2);
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_variants');
    }
};
