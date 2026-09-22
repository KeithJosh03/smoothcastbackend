<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('image_id');

            $table->morphs('imageable');

            $table->string('image_url');
            $table->boolean('isMain')->default(false);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};