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
                'type_id' => null, 
                'product_name' => 'Daiwa 18 EXIST G LT 3000 CXH',
                'description' =>  "DAIWA has held the same dream for 60 years. To bring to the world, the finest designed reel ever seen. A reel that is so light and easy to use that it feels like an extension of your body. A reel that is so strong that it will withstand the toughest conditions an angler and the environment can throw at it. The dream to create a reel that turns the contradiction of being LIGHT yet TOUGH on its head.
Now, after a long history of innovation, and endless challenges, that dream has become a reality. 2018 NEW EXIST. Here, for the anglers of the world, is a reel like we've never seen before. A reel designed for ultimate performance, joy and angling perfection."
            ],
            [
                'brand_id' => 1,
                'category_id' => 2,
                'type_id' => 1,
                'product_name' => 'CB NAGAMASA 115GM COL 04',
                'description' => "The CB. Nagamasa has a long flat body, center balance, and laser edge back design. The more you release the line tension when falling, the more the lure will receive water on a long flat surface, and the longer the horizontal slide will be due to the center balance, appealing to fish."
            ]
        ]);
    }
}
