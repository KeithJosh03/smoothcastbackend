<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if(!Schema::hasTable('specifications')){
            Schema::create('specifications', function (Blueprint $table) {
                $table->id('specification_ID');
                $table->foreignID('variantspecification_ID')
                    ->constrained('variant_specifications','variantspecification_ID');
                $table->string('specificationName',100);
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('specifications');
    }
};
