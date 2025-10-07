<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('setup_images', function (Blueprint $table) {
            $table->id('setup_img_id');
            $table
                ->foreignId('setup_id')
                ->constrained('setups', 'setup_id')
                ->onDelete('cascade');
            $table->string('url',100);
            $table->boolean('isMain');
        });
    }
    public function down(): void {
        Schema::dropIfExists('setup_images');
    }
};
