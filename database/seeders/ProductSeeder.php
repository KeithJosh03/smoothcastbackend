<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder {

    public function run(): void  {
        DB::table('products')->insert([
            [
            'brand_id' => 1 ,
            'category_id' => 1,
            'type_id' => 1, 
            'product_name' => 'Daiwa 18 EXIST G LT 3000 CXH',
            'base_price' => 27000.50,
            'description' =>  "DAIWA has held the same dream for 60 years. To bring to the world, the finest designed reel ever seen. A reel that is so light and easy to use that it feels like an extension of your body. A reel that is so strong that it will withstand the toughest conditions an angler and the environment can throw at it. The dream to create a reel that turns the contradiction of being LIGHT yet TOUGH on its head.

Now, after a long history of innovation, and endless challenges, that dream has become a reality. 2018 NEW EXIST. Here, for the anglers of the world, is a reel like we've never seen before. A reel designed for ultimate performance, joy and angling perfection."
            ],
            [                                                     
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 10600.50,
            'product_name' => '18 BLAST LT5000D-CXH',
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 2,
            'type_id' => 2,
            'product_name' => "CB NAGAMASA 115GM COL 04",
            'base_price' => 700.00,
            'description' => "The CB. Nagamasa has a long flat body, center balance, and laser edge back design. The more you release the line tension when falling, the more the lure will receive water on a long flat surface, and the longer the horizontal slide will be due to the center balance, appealing to fish."
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 4,
            'base_price' => 50750.00,
            'product_name' => "Daiwa Seaborg 500MJ-AT",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 7800.00,
            'product_name' => "19 LEXA LT5000D-CXH",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 43000.00,
            'product_name' => "20 SALTIGA (G) 8000P",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 4380.00,
            'product_name' => "20 TAMS LT2500",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 3,
            'base_price' => 4600.00,
            'product_name' => "20 TAMS LT4000-C",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 1,
            'type_id' => 1,
            'base_price' => 4200.00,
            'product_name' => "22Daiwa PT 150H BK",
            'description' => null
            ],
            [
            'brand_id' => 3,
            'category_id' => 2,
            'type_id' => 6,
            'base_price' => 280.00,
            'product_name' => "Bait Breath AJ-R Ajing Reversible (8/25)",
            'description' => null
            ],
            [
            'brand_id' => 2,
            'category_id' => 2,
            'type_id' => 5,
            'base_price' => 1850.00,
            'product_name' => "ACUP HAMMER HEAD 145MM/54G FLOATING COL 05",
            'description' => null
            ],
            [
            'brand_id' => 4,
            'category_id' => 2,
            'type_id' => 6,
            'base_price' => 290.00,
            'product_name' => "Gekkabijin Beam Stick Kiwami 2.2",
            'description' => null
            ],
            [
            'brand_id' => 2,
            'category_id' => 3,
            'type_id' => 7,
            'base_price' => 18000.00,
            'product_name' => "AOF K - FIBRE 76H",
            'description' => null
            ],
            [
            'brand_id' => 5,
            'category_id' => 3,
            'type_id' => 8,
            'base_price' => 7200.00,
            'product_name' => "BLACK EXPERT II SLOW JIGGING EBESJ II-B602MH",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 3,
            'type_id' => 9,
            'base_price' => 5800.00,
            'product_name' => "Daiwa Blast SLJ AP 63LS-S (Super Light Jigging JDM)",
            'description' => null
            ],
            [
            'brand_id' => 1,
            'category_id' => 3,
            'type_id' => 7,
            'base_price' => 4000.00,
            'product_name' => "Daiwa Lurenist 74ul-s rod",
            'description' => null
            ],
            [
            'brand_id' => 8,
            'category_id' => 4,
            'type_id' => 10,
            'base_price' => 180.00,
            'product_name' => "Decoy Bullet Sinker",
            'description' => "A standard bullet sinker that matches basic rigs such as Texas rigs and Carolina rigs. A protection tube is attached to the through the part to prevent line breaks. Even if it looks normal, we pay attention to the details!"
            ],
            [
            'brand_id' => 9,
            'category_id' => 4,
            'type_id' => 11,
            'base_price' => 65.00,
            'product_name' => "Mebao Accessory Box MBE1",
            'description' => "A multifunctional Bait and Accessory Box Made of thickened PP material that comes in 3 different configuration for the anglers preference. Its has a large capacity size of 175 x 105 x 36mm hat is very convenient to place on backpacks or tackle boxes."
            ],
            [
            'brand_id' => 10,
            'category_id' => 2,
            'type_id' => 2,
            'base_price' => 210.00,
            'product_name' => "Tukob Nomadic Piercer Ford Darting",
            'description' => null
            ],
        ]);
    }
}
