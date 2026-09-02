<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sku_variant_option', function (Blueprint $table) {
            $table
                ->foreignId('sku_id')
                ->constrained('product_skus', 'sku_id')
                ->cascadeOnDelete();

            $table
                ->foreignId('variant_option_id')
                ->constrained('variant_options', 'variant_option_id')
                ->cascadeOnDelete();

            $table->primary(['sku_id', 'variant_option_id'], 'sku_variant_option_primary');

            $table->index('variant_option_id', 'sku_variant_option_option_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sku_variant_option');
    }
};
