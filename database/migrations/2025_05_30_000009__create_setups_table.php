<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('setups', function (Blueprint $table) {
            $table->id('setup_id');
            $table->string('setup_name');
            $table->string('code_name');
            $table->text('description')
                ->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('value_discount',10,2);
            $table->string('type_discount',100);
        });
    }

    public function down(): void {
        Schema::dropIfExists('setups');
    }
};
