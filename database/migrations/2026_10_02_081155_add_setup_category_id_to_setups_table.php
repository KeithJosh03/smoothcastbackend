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
        Schema::table('setups', function (Blueprint $table) {
            $table->unsignedBigInteger('setup_category_id')->nullable()->after('setup_id');
            $table->foreign('setup_category_id')->references('id')->on('setup_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setups', function (Blueprint $table) {
            $table->dropForeign(['setup_category_id']);
            $table->dropColumn('setup_category_id');
        });
    }
};
