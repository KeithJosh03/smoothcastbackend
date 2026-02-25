<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setup_item_variants', function (Blueprint $table) {
            $table->id('setup_item_variant');
            $table
                ->foreignId('setup_id')
                ->constrained('setups', 'setup_id')
                ->onDelete('cascade');
            $table
                ->foreignId('variant_option_id')
                ->constrained('variant_options', 'variant_option_id')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_item_variants');
    }
};