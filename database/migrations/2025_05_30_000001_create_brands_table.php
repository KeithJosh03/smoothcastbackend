<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('brands', function (Blueprint $table) {
            $table->id('brand_id');
            $table->string('brand_name',100);
        });
    }

    public function down(): void {
        Schema::dropIfExists('brands');
    }
};