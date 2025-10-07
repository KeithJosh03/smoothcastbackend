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
            'full_model_name' => "Daiwa 18 EXIST G LT 3000 CXH",
            'product_price' => 27000.00
            ],
            [
            'product_id' => 2,
            'full_model_name' => "18 BLAST LT5000D-CXH",
            'product_price' => 10600.00
            ],
            [
            'product_id' => 3,
            'full_model_name' => "CB NAGAMASA 115GM COL 04",
            'product_price' => 700.00
            ],
            [
            'product_id' => 4,
            'full_model_name' => "Daiwa Seaborg 500MJ-AT",
            'product_price' => 50750.00
            ],
            [
            'product_id' => 5,
            'full_model_name' => "19 LEXA LT5000D-CXH",
            'product_price' => 7800.00
            ],
            [
            'product_id' => 6,
            'full_model_name' => "20 SALTIGA (G) 8000P",
            'product_price' => 43000.00
            ],
            [
            'product_id' => 7,
            'full_model_name' => "20 TAMS LT2500",
            'product_price' => 4380.00
            ],
            [
            'product_id' => 8,
            'full_model_name' => "20 TAMS LT4000-C",
            'product_price' => 4600.00
            ],
            [
            'product_id' => 9,
            'full_model_name' => "22Daiwa PT 150H BK",
            'product_price' => 4200.00
            ],
            [
            'product_id' => 10,
            'full_model_name' => "Bait Breath AJ-R Ajing Reversible (8/25)",
            'product_price' => 280.00
            ],
            [
            'product_id' => 11,
            'full_model_name' => "ACUP HAMMER HEAD 145MM/54G FLOATING COL 05",
            'product_price' => 1850.00
            ],
            [
            'product_id' => 12,
            'full_model_name' => "Gekkabijin Beam Stick Kiwami 2.2",
            'product_price' => 290.00
            ],
            [
            'product_id' => 13,
            'full_model_name' => "AOF K - FIBRE 76H",
            'product_price' => 18000.00
            ],
            [
            'product_id' => 14,
            'full_model_name' => "BLACK EXPERT II SLOW JIGGING EBESJ II-B602MH",
            'product_price' => 7200.00
            ],
            [
            'product_id' => 15,
            'full_model_name' => "Daiwa Blast SLJ AP 63LS-S (Super Light Jigging JDM)",
            'product_price' => 5800.00
            ],
            [
            'product_id' => 16,
            'full_model_name' => "Daiwa Lurenist 74ul-s rod",
            'product_price' => 4000.00
            ],
            [
            'product_id' => 17,
            'full_model_name' => "Decoy Bullet Sinker 7g",
            'product_price' => 180.00
            ],
            [
            'product_id' => 17,
            'full_model_name' => "Decoy Bullet Sinker 9g",
            'product_price' => 180.00
            ],
            [
            'product_id' => 17,
            'full_model_name' => "Decoy Bullet Sinker 11g",
            'product_price' => 180.00
            ],
            [
            'product_id' => 17,
            'full_model_name' => "Decoy Bullet Sinker 14g",
            'product_price' => 180.00
            ],
            [
            'product_id' => 18,
            'full_model_name' => "MBE1-103L",
            'product_price' => 65.00
            ],
            [
            'product_id' => 18,
            'full_model_name' => "MBE1-203L",
            'product_price' => 65.00
            ],
            [
            'product_id' => 18,
            'full_model_name' => "MBE1-309L",
            'product_price' => 75.00
            ],
            [
            'product_id' => 19,
            'full_model_name' => "Nomadic Piercer 1g",
            'product_price' => 210.00
            ],
            [
            'product_id' => 19,
            'full_model_name' => "Nomadic Piercer 1.3g",
            'product_price' => 210.00
            ],
            [
            'product_id' => 19,
            'full_model_name' => "Nomadic Piercer 1.5g",
            'product_price' => 210.00
            ],
            [
            'product_id' => 19,
            'full_model_name' => "Nomadic Piercer 2g",
            'product_price' => 250.00
            ],
            [
            'product_id' => 19,
            'full_model_name' => "Nomadic Piercer 2.5g",
            'product_price' => 260.00
            ],
        ]);
    }
}
