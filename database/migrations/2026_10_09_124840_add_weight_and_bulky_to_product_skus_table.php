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
        Schema::table('product_skus', function (Blueprint $table) {
            $table->integer('weight')->nullable()->comment('Weight in grams. Overrides product weight if set.');
            $table->boolean('is_bulky')->nullable()->comment('Overrides product is_bulky if set.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_skus', function (Blueprint $table) {
            $table->dropColumn(['weight', 'is_bulky']);
        });
    }
};
