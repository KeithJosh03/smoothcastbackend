<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('product_variant_types', function (Blueprint $table) {
            $table->id('variant_type_id');
            $table
                ->foreignId('product_id')
                ->constrained('products','product_id')
                ->onDelete('cascade');
            $table->string('variant_type_name',200);
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_variant_types');
    }
};
