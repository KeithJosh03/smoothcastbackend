<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_ID');
            $table->string('category_name',100);
        });
    }

    public function down(): void {
        Schema::dropIfExists('categories');
    }
};
