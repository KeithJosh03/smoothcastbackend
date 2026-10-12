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
            $table->unsignedInteger('weight')->nullable();
            $table->boolean('is_bulky')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setups', function (Blueprint $table) {
            $table->dropColumn(['weight', 'is_bulky']);
        });
    }
};
