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
            ['brand_name' => 'Smith'],
            ['brand_name' => 'Bait Breath'],
            ['brand_name' => 'Gekkabijin'],
            ['brand_name' => 'Ecooda'],
            ['brand_name' => 'Moasmdoadmoasmdioamsdi']
        ]);
    }
}
