<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variant_options', function (Blueprint $table) {
            $table->id('variant_option_id');

            $table
                ->foreignId('variant_type_id')
                ->constrained('product_variant_types', 'variant_type_id')
                ->cascadeOnDelete();

            $table->string('variant_value', 225);

            $table->timestamps();

            $table->index(
                ['variant_type_id', 'variant_value'],
                'variant_options_type_value_index'
            );
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('variant_options');
        Schema::enableForeignKeyConstraints();
    }
};
