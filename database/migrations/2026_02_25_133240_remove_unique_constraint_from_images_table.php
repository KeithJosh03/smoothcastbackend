<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('images')) {
            return;
        }

        $indexExists = collect(
            DB::select('SHOW INDEX FROM `images` WHERE Key_name = ?', ['images_imageable_id_imageable_type_unique'])
        )->isNotEmpty();

        if (! $indexExists) {
            return;
        }

        Schema::table('images', function (Blueprint $table) {
            $table->dropUnique('images_imageable_id_imageable_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-adding the unique constraint would fail if there are multiple images for a model.
    }
};
