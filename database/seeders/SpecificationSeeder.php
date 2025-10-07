<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecificationSeeder extends Seeder {
    public function run(): void {
    db::table('specifications')->insert([
        [
        'product_id' => 1,
        'specification' => 
        'Gear Ratio: 6.2:1
        Max Drag: 22lb
        Mono Cap: 8/160,10/130
        Braid Cap: 8/220,10/185
        Ball Bearing: 12 CRBB+1
        Inches per handle turn: 36.8
        Weight: 6.5oz'
        ],
        [
        'product_id' => 3,
        'specification' => 
        'Length: 185MM
        Weight: 115GM
        Color: COL 04'
        ],
        [
        'product_id' => 5,
        'specification' => 
        'Line Retrieve: 105
        Gear Ratio: 6.2
        Max Drag: 12
        Weight: 290
        Braid Cap: #2-350, #2.5-300, #3-210
        Bearing: 5 / 1'
        ],
        [
        'product_id' => 6,
        'specification' => 
        'Bearings: 12BB (6CRBB-2MSBB)
        Capacity PE: PE4 - 300m
        Max Drag: 25kg
        Gear: 4.8
        Pickup: 92cm
        Weight: 645g'
        ],
        [
        'product_id' => 7,
        'specification' => 
        'Weight: 10kg
        Braid Line Capacity: (#PE/m): #1.5/200
        Gear Ratio: 5.2:1
        Water: Salt Water
        Ball Bearing: 6BB + 1RB
        Line Per Handle Turn: 82cm
        Weight: 275g'
        ],
        [
        'product_id' => 2,
        'specification' => 
        'Winding length (cm / one rotation of handle):105
        Gear ratio: 6.2
        Own weight (g): 285
        Maximum drag force (kg): 12
        PE (No.-m) : 2.5-300, 3-210
        Bearing (ball / roller): 6/1'
        ],
        [
        'product_id' => 7,
        'specification' => 
        '20 TAMS LT2500
        Max Drag : 10kg
        Braid Line Capacity : (#PE/m): #0.8/200
        Gear Ratio : 5.3:1
        Water : Salt Water
        Ball Bearing : 6BB + 1RB
        Line Per Handle Turn : 75cm
        Weight : 230g',
        ],

        [
        'product_id' => 8,
        'specification' => 
        'Max Drag : 10kg
        Braid Line Capacity : (#PE/m): #1.5/200
        Gear Ratio : 5.2:1
        Water : Salt Water
        Ball Bearing : 6BB + 1RB
        Line Per Handle Turn : 82cm
        Weight : 275g',
        ],
        [
        'product_id' => 13,
        'specification' => 
        'Length: 7ft6in
        Section: 2 pc
        Lure Weight: 50-120g
        Line: pe 6-7
        Max Drag: 8kg'
        ],
        [
        'product_id' => 14,
        'specification' => 
        'EBESJ II - B602MH Overhead Rod
        Length: 6ft
        Jig Max: 400g
        Line: PE 2-4
        Section: 2
        Test: 18kg'
        ]
    ]);
    }
}
