<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    
        Schema::create('specifications', function (Blueprint $table) {
            $table->id('specs_id');
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');
            $table->text('specification');
        });
    }

    public function down(): void  {
        Schema::dropIfExists('specifications');
    }
};
