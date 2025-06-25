<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('specification_values', function (Blueprint $table) {
            $table->id('specificationValue_ID');
            $table->string('specificationValue',100);
        });
    }

    public function down(): void {
        Schema::dropIfExists('specification_values');
    }
};
