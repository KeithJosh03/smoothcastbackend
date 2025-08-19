<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CategorySeeder extends Seeder {
    public function run(): void {
        DB::table('categories')->insert([
            ['category_name' => 'Reels'],
            ['category_name' => 'Lures'],
            ['category_name' => 'Rods'],
        ]);
    }
}
