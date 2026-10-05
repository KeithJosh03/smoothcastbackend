<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('promotion_category');

        DB::statement("ALTER TABLE promotions MODIFY COLUMN apply_to ENUM('ALL', 'PRODUCT', 'SETUP') NOT NULL DEFAULT 'ALL'");

        Schema::create('promotion_setup', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained('promotions', 'promotion_id')->onDelete('cascade');
            $table->foreignId('setup_id')->constrained('setups', 'setup_id')->onDelete('cascade');
            $table->primary(['promotion_id', 'setup_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_setup');

        DB::statement("ALTER TABLE promotions MODIFY COLUMN apply_to ENUM('ALL', 'CATEGORY', 'PRODUCT') NOT NULL DEFAULT 'ALL'");

        Schema::create('promotion_category', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained('promotions', 'promotion_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
            $table->primary(['promotion_id', 'category_id']);
        });
    }
};
