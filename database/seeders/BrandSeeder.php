<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BrandSeeder extends Seeder {
    public function run(): void {
        DB::table('brands')->insert([
            [
            'brand_name' => 'Daiwa',
            'image_url' => 'daiwa.png'
            ],
            [
            'brand_name' => 'Smith',
            'image_url' => 'smith.png'
            ],
            [
            'brand_name' => 'Bait Breath',
            'image_url' => 'baitbreath.png'
            ],
            [
            'brand_name' => 'Gekkabijin',
            'image_url' => null
            ],
            [
            'brand_name' => 'Ecooda',
            'image_url' => null
            ],
            [
            'brand_name' => 'Abu Garcia',
            'image_url' => 'abugarcia.svg'
            ],
            [
            'brand_name' => 'Shimano',
            'image_url' => 'shimano.png'
            ],
            [
            'brand_name' => 'Decoy',
            'image_url' => 'decoy.png'
            ],
            [
            'brand_name' => 'MeBao',
            'image_url' => 'mebaologo.png'
            ],
            [
            'brand_name' => 'Tukob',
            'image_url' => 'tukob.png'
            ]
        ]);
    }
}


