<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder {
    public function run(): void {
        DB::table('product_images')->insert([
            [
            'variant_id' => 1,
            'url' => 'daiwa18exists.jpg',
            'isMain' => true 
            ],
            [
            'variant_id' => 1,
            'url' => 'daiwa18exists2.jpg',
            'isMain' => false
            ],
            [
            'variant_id' => 2,
            'url' => '18blastlt5000d-cxh.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 3,
            'url' => 'cbnagamasa11gmcol04.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 4,
            'url' => 'daiwaseabog500mj0a.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 4,
            'url' => 'daiwaseabog500mj0a2.jpeg',
            'isMain' => false
            ],
            [
            'variant_id' => 5,
            'url' => '19lexalt500d-cxh.jpeg',
            'isMain' => true
            ],
            [
            'variant_id' => 6,
            'url' => '20tamslt4000c.png',
            'isMain' => true
            ],
            [
            'variant_id' => 7,
            'url' => '20tamslt2500.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 8,
            'url' => '20tamslt4000-c.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 9,
            'url' => '22daiwapt150hbk.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 10,
            'url' => 'baitbreathaj-rajingreversible825.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 11,
            'url' => 'acuphammerhead145mmfloatingcol05.jpeg',
            'isMain' => true
            ],
            [
            'variant_id' => 12,
            'url' => 'gbstickkiwami22.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 13,
            'url' => 'aofkfibre76h.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 14,
            'url' => 'beIIsjjjbesjiib602mh.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 15,
            'url' => 'dbsap63lss.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 16,
            'url' => 'dl74uulsrod.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 16,
            'url' => 'dl74uulsrod.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 17,
            'url' => 'decoy-ds5-bullet-weights-7199-p.png',
            'isMain' => true
            ],
            [
            'variant_id' => 18,
            'url' => 'TWC_4989540820049.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 19,
            'url' => 'TWC_4989540820056.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 20,
            'url' => '81DLlWeXtoL._UF1000,1000_QL80_.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 21,
            'url' => 'mebaoaccessboxmb309l.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 24,
            'url' => '475760546_122143862732533717_551827842000346744_n.jpg',
            'isMain' => true
            ],
            [
            'variant_id' => 24,
            'url' => '475756940_122142803060533717_3314348891614614446_n.jpg',
            'isMain' => true
            ],
            
        ]);
    }
}