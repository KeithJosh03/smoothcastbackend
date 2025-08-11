<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BrandSeeder extends Seeder {
    public function run(): void {
        DB::table('brands')->insert([
            ['brand_name' => 'Daiwa'],
            ['brand_name' => 'Smith']
        ]);
    }
}
