<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder {
    public function run(): void {
    db::table('features')->insert([
        [
        'features' => "ATD drag technology reinforced by 30%, full-size drag button for stability",
        'product_id' => 1
        ],
        [
        'features' => "Bearings: 12 + 1 including 2 Mag Sealed and 10 CRBBs",
        'product_id' => 1
        ],
        [
        'features' => "Monocoque magnesium body and ZAION Air Rotor",
        'product_id' => 1
        ],
        [
        'features' => "CrossWrap line lay",
        'product_id' => 1
        ],
        [
        'features' => "Supplied in a neoprene sleeve with mesh sides",
        'product_id' => 1
        ],
        [
        'features' => "Supplied in a neoprene sleeve with mesh sides",
        'product_id' => 1
        ],
        [
        'features' => "Japan Domestic Model.",
        'product_id' => 4
        ],
        [
        'features' => "Gear Ratio: 3.7",
        'product_id' => 4
        ],
        [
        'features' => "Winding length (cm / handle 1 rotation): 57",
        'product_id' => 4
        ],
        [
        'features' => "Weight - (g): 1010",
        'product_id' => 4
        ],
        [
        'features' => "Max Drag (kg): 23",
        'product_id' => 4
        ],
        [
        'features' => "Line Capacity PE (No.-m): 4-500-5-400-6-300",
        'product_id' => 4
        ],
        [
        'features' => "Line Capacity nylon (No.-m): 6-350-7-300-8-250",
        'product_id' => 4
        ],
        [
        'features' => "Bearings: 22/1",
        'product_id' => 4
        ],
        [
        'features' => "Max Winding Power (kg): 90 (100)",
        'product_id' => 4
        ],        
        [
        'features' => "JAFS standard speed (m/min): Hi230-Lo150",
        'product_id' => 4
        ],        
        [
        'features' => "Normal hoisting speed at 1kg load (m/min): Hi180(200)-Lo125(140)",
        'product_id' => 4
        ],        
        [
        'features' => "JAFS-Standard hoisting force-(kg): 22",
        'product_id' => 4
        ],
        [
        'features' => "110m Baitcast Handle",
        'product_id' => 9
        ],  
        [
        'features' => "Aluminium Baitcast Handle",
        'product_id' => 9
        ],  
        [
        'features' => "Aluminium Body",
        'product_id' => 9
        ],        
        [
        'features' => "EVA Knob – Round Ball Knob",
        'product_id' => 9
        ],  
        [
        'features' => "Number of pieces: 10 pcs",
        'product_id' => 10
        ], 
        [
        'features' => "List price: 280pesos",
        'product_id' => 10
        ], 
        [
        'features' => "Size: 2 inches",
        'product_id' => 10
        ], 
        [
        'features' => "Total length: 5.4cm",
        'product_id' => 10
        ], 
        [
        'features' => "● A bullet-type sinker that matches well with worms and is ideal for use with Texas rigs",
        'product_id' => 17
        ], 
        [
        'features' => "● Equipped with a protective tube to prevent line breaks",
        'product_id' => 17
        ], 
        [
        'features' => "● Gun black finish that is familiar to all worm colours and does not give a sense of discomfort",
        'product_id' => 17
        ], 
        [
        'features' => "● Uses a hard material (lead alloy) that increases sensitivity",
        'product_id' => 17
        ], 
        [
        'features' => "Mebao Accessory Box",
        'product_id' => 18
        ], 
        [
        'features' => "MBE1-103L (5 permanent vertical slots)
Price: 65php cash 70php srp",
        'product_id' => 18
        ], 
        [
        'features' => "MBE1-203L (Empty Case)
Price: 65php cash 70php srp",
        'product_id' => 18
        ], 
        [
        'features' => "MBE1-309L (Slotted Foam for Hooks)
Price: 75php cash 80php srp",
        'product_id' => 18
        ], 
    ]
    );
    }
}
