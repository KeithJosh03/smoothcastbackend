<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class CategoryTypesSeeder extends Seeder {
    public function run(): void {
     DB::table('category_types')->insert([
        [
        'category_id' => 1,
        'type_name' => 'Overhead/Baitcasting Reels'
        ],
        [
        'category_id' => 2,
        'type_name' => 'Jigs'
        ],
        [
        'category_id' => 1,
        'type_name' => 'Spinning Reels'
        ],
        [
        'category_id' => 1,
        'type_name' => 'Electric Reels'
        ]
     ]);
    }
}
