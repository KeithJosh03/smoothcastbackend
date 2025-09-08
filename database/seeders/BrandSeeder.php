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
            'imageUrl' => 'daiwa.png'
            ],
            [
            'brand_name' => 'Smith',
            'imageUrl' => 'smith.png'
            ],
            [
            'brand_name' => 'Bait Breath',
            'imageUrl' => 'baitbreath.png'
            ],
            [
            'brand_name' => 'Gekkabijin',
            'imageUrl' => null
            ],
            [
            'brand_name' => 'Ecooda',
            'imageUrl' => null
            ],
            [
            'brand_name' => 'Abu Garcia',
            'imageUrl' => 'abugarcia.svg'
            ],
            [
            'brand_name' => 'Shimano',
            'imageUrl' => 'shimano.png'
            ],
            [
            'brand_name' => 'Decoy',
            'imageUrl' => 'decoy.png'
            ],
            [
            'brand_name' => 'MeBao',
            'imageUrl' => 'mebaologo.png'
            ],
            [
            'brand_name' => 'Tukob',
            'imageUrl' => 'tukob.png'
            ]
        ]);
    }
}


