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
            $table->decimal('retail_price', 10, 2)->nullable()->after('pricing_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setups', function (Blueprint $table) {
            $table->dropColumn('retail_price');
        });
    }
};
