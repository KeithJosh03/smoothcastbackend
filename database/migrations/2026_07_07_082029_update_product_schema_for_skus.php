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
        // 1. Update products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('product_id');
            }
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->nullable()->default(0)->after('sku');
            }
        });

        // 2. Update variant_options table (remove price_adjustment if it exists)
        if (Schema::hasColumn('variant_options', 'price_adjustment')) {
            Schema::table('variant_options', function (Blueprint $table) {
                $table->dropColumn('price_adjustment');
            });
        }

        // 3. Create product_skus table (drop existing if created by older migrations)
        Schema::dropIfExists('sku_variant_option');
        Schema::dropIfExists('product_skus');
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id('sku_id');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->string('sku_code')->unique();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Create sku_variant_option pivot table
        Schema::create('sku_variant_option', function (Blueprint $table) {
            $table->foreignId('sku_id')->constrained('product_skus', 'sku_id')->onDelete('cascade');
            $table->foreignId('variant_option_id')->constrained('variant_options', 'variant_option_id')->onDelete('cascade');
            
            // Composite primary key
            $table->primary(['sku_id', 'variant_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sku_variant_option');
        Schema::dropIfExists('product_skus');

        Schema::table('variant_options', function (Blueprint $table) {
            $table->decimal('price_adjustment', 10, 2)->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock_quantity']);
        });
    }
};
