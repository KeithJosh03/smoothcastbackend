<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    
        Schema::create('specifications', function (Blueprint $table) {
            $table->id('specs_id');
            $table
                ->foreignId('variant_id')
                ->constrained('product_variants', 'variant_id')
                ->onDelete('cascade');
            $table->string('specs_name',100);
            $table->string('value',500);
        });
    }

    public function down(): void  {
        Schema::dropIfExists('specifications');
    }
};
