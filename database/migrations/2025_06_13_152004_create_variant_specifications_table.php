<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        if (!Schema::hasTable('variant_specifications')) {
            Schema::create('variant_specifications', function (Blueprint $table) {
                $table->id('variantspecification_ID');
                $table->foreignId('product_ID')
                    ->constrained('products','product_ID');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('variant_specifications');
    }
};
