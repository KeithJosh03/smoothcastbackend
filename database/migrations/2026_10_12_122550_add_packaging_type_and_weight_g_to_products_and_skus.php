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
            $table->enum('packaging_type', ['POUCH_S', 'POUCH_L', 'BOX_S', 'BOX_M', 'BOX_L', 'LONG_PARCEL'])
                ->default('POUCH_S')
                ->after('sku_code');
            $table->integer('weight_g')->nullable()->default(0)->after('packaging_type');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->enum('packaging_type', ['POUCH_S', 'POUCH_L', 'BOX_S', 'BOX_M', 'BOX_L', 'LONG_PARCEL'])
                ->default('POUCH_S')
                ->nullable()
                ->after('base_price');
            $table->integer('weight_g')->nullable()->default(0)->after('packaging_type');
        });

        // Initialize weight_g from weight if existing
        \DB::statement("UPDATE product_skus SET weight_g = COALESCE(weight, 0)");
        \DB::statement("UPDATE products SET weight_g = COALESCE(weight, 0)");

        // Sync initial is_bulky for BOX_L and LONG_PARCEL
        \DB::statement("UPDATE product_skus SET is_bulky = 1 WHERE packaging_type IN ('BOX_L', 'LONG_PARCEL')");
        \DB::statement("UPDATE products SET is_bulky = 1 WHERE packaging_type IN ('BOX_L', 'LONG_PARCEL')");
    }

    public function down(): void
    {
        Schema::table('product_skus', function (Blueprint $table) {
            $table->dropColumn(['packaging_type', 'weight_g']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['packaging_type', 'weight_g']);
        });
    }
};
