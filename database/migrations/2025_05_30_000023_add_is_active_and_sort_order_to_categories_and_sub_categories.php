<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('category_name');
            }
            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sub_category_name');
            }
            if (!Schema::hasColumn('sub_categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(array_filter(['is_active', 'sort_order'], fn($col) => Schema::hasColumn('categories', $col)));
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn(array_filter(['is_active', 'sort_order'], fn($col) => Schema::hasColumn('sub_categories', $col)));
        });
    }
};