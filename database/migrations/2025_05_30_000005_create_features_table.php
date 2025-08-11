<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('features', function (Blueprint $table) {
            $table->id('feature_id');
            $table
                ->foreignId('product_id')
                ->constrained('products','product_id')
                ->onDelete('cascade');
            $table->string('feat_name');
            $table->string('value');
        });
    }

    public function down(): void {
        Schema::dropIfExists('features');
    }
};
