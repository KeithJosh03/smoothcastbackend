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
        'specs_name' => 'Gear Ratio',
        'specs_value' => '6.2:1'
        ],
        [
        'product_id' => 1,
        'specs_name' => 'Max Drag',
        'specs_value' => '22lb'
        ],
        [
        'product_id' => 1,
        'specs_name' => 'Mono Cap',
        'specs_value' => '8/160,10/130'
        ],
        [
        'product_id' => 1,
        'specs_name' => 'Braid Cap',
        'specs_value' => '8/220,10/185'
        ],
        [
        'product_id' => 1,
        'specs_name' => 'Ball Bearing',
        'specs_value' => '12 CRBB+1'
        ],
        [
        'product_id' => 1,
        'specs_name' => 'Inches per handle turn',
        'specs_value' => '36.8'
        ],
        [
        'product_id' => 1,
        'specs_name' => 'Weight',
        'specs_value' => '6.5oz'
        ],
        [
        'product_id' => 3,
        'specs_name' => 'Length',
        'specs_value' => '185MM'
        ],
        [
        'product_id' => 3,
        'specs_name' => 'Weight',
        'specs_value' => '115GM'
        ],
        [
        'product_id' => 3,
        'specs_name' => 'Color',
        'specs_value' => 'COL 04'
        ],
        [
        'product_id' => 5,
        'specs_name' => 'Line Retrieve',
        'specs_value' => '105'
        ],
        [
        'product_id' => 5,
        'specs_name' => 'Gear Ratio',
        'specs_value' => '6.2'
        ],
        [
        'product_id' => 5,
        'specs_name' => 'Max Drag',
        'specs_value' => '12'
        ],
        [
        'product_id' => 5,
        'specs_name' => 'Weight',
        'specs_value' => '290'
        ],
        [
        'product_id' => 5,
        'specs_name' => 'Braid Cap',
        'specs_value' => '#2-350, #2.5-300, #3-210'
        ],
        [
        'product_id' => 5,
        'specs_name' => 'Bearing',
        'specs_value' => '5 / 1'
        ],
        [
        'product_id' => 6,
        'specs_name' => 'Bearings',
        'specs_value' => '12BB (6CRBB-2MSBB)'
        ],
        [
        'product_id' => 6,
        'specs_name' => 'Capacity PE',
        'specs_value' => 'PE4 - 300m'
        ],
        [
        'product_id' => 6,
        'specs_name' => 'Max Drag',
        'specs_value' => '25kg'
        ],
        [
        'product_id' => 6,
        'specs_name' => 'Gear',
        'specs_value' => '4.8'
        ],
        [
        'product_id' => 6,
        'specs_name' => 'Pickup',
        'specs_value' => '92cm'
        ],
        [
        'product_id' => 6,
        'specs_name' => 'Weight',
        'specs_value' => '645g'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Weight',
        'specs_value' => '10kg'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Braid Line Capacity',
        'specs_value' => '(#PE/m): #1.5/200'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Gear Ratio',
        'specs_value' => '5.2:1'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Water',
        'specs_value' => 'Salt Water'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Ball Bearing',
        'specs_value' => '6BB + 1RB'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Line Per Handle Turn',
        'specs_value' => '82cm'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Weight',
        'specs_value' => '275g'
        ],
        [
        'product_id' => 2,
        'specs_name' => 'Winding length (cm / one rotation of handle)',
        'specs_value' => '105'
        ],
        [
        'product_id' => 2,
        'specs_name' => 'Gear ratio',
        'specs_value' => '6.2'
        ],

        [
        'product_id' => 2,
        'specs_name' => 'Own weight (g)',
        'specs_value' => '285'
        ],
        [
        'product_id' => 2,
        'specs_name' => 'Maximum drag force (kg)',
        'specs_value' => '12'
        ],
        [
        'product_id' => 2,
        'specs_name' => 'PE (No.-m)',
        'specs_value' => ' 2.5-300, 3-210'
        ],
        [
        'product_id' => 2,
        'specs_name' => 'Bearing (ball / roller)',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'PE (No.-m)',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Own weight (g)',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 7,
        'specs_name' => 'Gear ratio',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 8,
        'specs_name' => 'Own weight (g)',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 8,
        'specs_name' => 'Bearing (ball / roller)',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 8,
        'specs_name' => 'Weight',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 8,
        'specs_name' => 'Ball Bearing',
        'specs_value' => '6/1'
        ],
        [
        'product_id' => 13,
        'specs_name' => 'Length',
        'specs_value' => '7ft6in/1'
        ],
        [
        'product_id' => 13,
        'specs_name' => 'Section',
        'specs_value' => '2 pc'
        ],
        [
        'product_id' => 13,
        'specs_name' => 'Lure Weight',
        'specs_value' => '50-120g'
        ],
        [
        'product_id' => 14,
        'specs_name' => 'Length',
        'specs_value' => '6ft'
        ],
        [
        'product_id' => 14,
        'specs_name' => 'Jig Max',
        'specs_value' => '400g'
        ],
        [
        'product_id' => 14,
        'specs_name' => 'Line',
        'specs_value' => 'PE 2-4'
        ],
    ]);
    }
}
