<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetupImageSeeder extends Seeder {
    public function run(): void {
        DB::table('setup_images')->insert([
            [
            'setup_id' => 1,
            'url' => '/mmd1.jpg',
            'isMain' => 1 
            ]
        ]);
    }   
}
