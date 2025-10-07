<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id('discount_id');
            $table
                ->foreignId('variant_id')
                ->constrained('product_variants', 'variant_id')
                ->onDelete('cascade');
            $table->date('startDate');
            $table->date('endDate');
            $table->string('discount_type');
            $table->string('discount_value');
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_discounts');
    }
};
