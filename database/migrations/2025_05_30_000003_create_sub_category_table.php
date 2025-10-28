<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(): void{
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id('sub_category_id');
            $table->foreignId('category_id')
                ->constrained('categories','category_id')
                ->onDelete('cascade');
            $table->string('sub_category_name',100);
        });
    }
    public function down(): void{
    
        Schema::dropIfExists('sub_category');
    }
};
