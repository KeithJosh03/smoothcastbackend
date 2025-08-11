<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    
    public function up(): void{
        Schema::create('category_types', function (Blueprint $table) {
            $table->id('type_id');
            $table->foreignId('category_id')
                ->constrained('categories','category_id')
                ->onDelete('cascade');
            $table->string('type_name',100);
        });
    }
    public function down(): void{
    
        Schema::dropIfExists('category_types');
    }
};
