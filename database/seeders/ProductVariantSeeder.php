<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder {
    public function run(): void {
        DB::table('product_variants')->insert([
            [
            'product_id' => 1,
            'name' => "Daiwa 18 EXIST G LT 3000 CXH",
            'price' => 27000.00
            ],
            [
            'product_id' => 2,
            'name' => "18 BLAST LT5000D-CXH",
            'price' => 10600.00
            ],
            [
            'product_id' => 3,
            'name' => "CB NAGAMASA 115GM COL 04",
            'price' => 700.00
            ],
            [
            'product_id' => 4,
            'name' => "Daiwa Seaborg 500MJ-AT",
            'price' => 50750.00
            ],
            [
            'product_id' => 5,
            'name' => "19 LEXA LT5000D-CXH",
            'price' => 7800.00
            ],
            [
            'product_id' => 6,
            'name' => "20 SALTIGA (G) 8000P",
            'price' => 43000.00
            ],
            [
            'product_id' => 7,
            'name' => "20 TAMS LT2500",
            'price' => 4380.00
            ],
            [
            'product_id' => 8,
            'name' => "20 TAMS LT4000-C",
            'price' => 4600.00
            ],
            [
            'product_id' => 9,
            'name' => "22Daiwa PT 150H BK",
            'price' => 4200.00
            ],
            [
            'product_id' => 10,
            'name' => "Bait Breath AJ-R Ajing Reversible (8/25)",
            'price' => 280.00
            ],
            [
            'product_id' => 11,
            'name' => "ACUP HAMMER HEAD 145MM/54G FLOATING COL 05",
            'price' => 1850.00
            ],
            [
            'product_id' => 12,
            'name' => "Gekkabijin Beam Stick Kiwami 2.2",
            'price' => 290.00
            ],
            [
            'product_id' => 13,
            'name' => "AOF K - FIBRE 76H",
            'price' => 18000.00
            ],
            [
            'product_id' => 14,
            'name' => "BLACK EXPERT II SLOW JIGGING EBESJ II-B602MH",
            'price' => 7200.00
            ],
            [
            'product_id' => 15,
            'name' => "Daiwa Blast SLJ AP 63LS-S (Super Light Jigging JDM)",
            'price' => 5800.00
            ],
            [
            'product_id' => 16,
            'name' => "Daiwa Lurenist 74ul-s rod",
            'price' => 4000.00
            ],
            [
            'product_id' => 17,
            'name' => "Decoy Bullet Sinker 7g",
            'price' => 180.00
            ],
            [
            'product_id' => 17,
            'name' => "Decoy Bullet Sinker 9g",
            'price' => 180.00
            ],
            [
            'product_id' => 17,
            'name' => "Decoy Bullet Sinker 11g",
            'price' => 180.00
            ],
            [
            'product_id' => 17,
            'name' => "Decoy Bullet Sinker 14g",
            'price' => 180.00
            ],
            [
            'product_id' => 18,
            'name' => "MBE1-103L",
            'price' => 65.00
            ],
            [
            'product_id' => 18,
            'name' => "MBE1-203L",
            'price' => 65.00
            ],
            [
            'product_id' => 18,
            'name' => "MBE1-309L",
            'price' => 75.00
            ],
            [
            'product_id' => 19,
            'name' => "Nomadic Piercer 1g",
            'price' => 210.00
            ],
            [
            'product_id' => 19,
            'name' => "Nomadic Piercer 1.3g",
            'price' => 210.00
            ],
            [
            'product_id' => 19,
            'name' => "Nomadic Piercer 1.5g",
            'price' => 210.00
            ],
            [
            'product_id' => 19,
            'name' => "Nomadic Piercer 2g",
            'price' => 250.00
            ],
            [
            'product_id' => 19,
            'name' => "Nomadic Piercer 2.5g",
            'price' => 260.00
            ],
        ]);
    }
}
