<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unsignedBigInteger('setup_id')->nullable()->after('cart_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->cascadeOnDelete();
            $table->foreign('setup_id')
                ->references('setup_id')
                ->on('setups')
                ->cascadeOnDelete();
            $table->index(['cart_id', 'setup_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['setup_id']);
            $table->dropForeign(['product_id']);
            $table->dropIndex(['cart_id', 'setup_id']);
            $table->dropColumn('setup_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
};
