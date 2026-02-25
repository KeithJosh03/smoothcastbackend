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
        Schema::create('setups', function (Blueprint $table) {
            $table->id('setup_id');
            $table->string('setup_name', 255);
            $table->decimal('setup_price', 10, 2);
            $table->text('description')
                ->nullable();
            $table->boolean('available')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setups');
    }
};