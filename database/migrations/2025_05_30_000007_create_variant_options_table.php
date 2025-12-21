<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id('variant_option_id');
            $table
                ->foreignId('variant_type_id')
                ->constrained('product_variant_types','variant_type_id')
                ->onDelete('cascade');
            $table->decimal('price_adjustment',10,2);
            $table->string('image_url',225);
            $table->string('variant_option_value',225);
        });
    }
    public function down(): void {
        Schema::dropIfExists('variant_options');
    }
};
