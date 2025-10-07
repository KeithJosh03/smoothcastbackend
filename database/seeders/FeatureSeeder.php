<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder {
    public function run(): void {
    db::table('features')->insert([
        [
        'features' => "ATD drag technology reinforced by 30%, full-size drag button for stability
Bearings: 12 + 1 including 2 Mag Sealed and 10 CRBBs
Monocoque magnesium body and ZAION Air Rotor
Ultralight Aluminium Air Spool
CrossWrap line lay
Ultralight one-piece unscrewable and slit Air Handle in aluminium
Supplied in a neoprene sleeve with mesh sides",
        'product_id' => 1
        ],
        [
        'features' => 
        "Japan Domestic Model.
        Gear Ratio: 3.7
        Winding length (cm / handle 1 rotation): 57
        Weight - (g): 1010
        Max Drag (kg): 23
        Line Capacity PE (No.-m): 4-500-5-400-6-300
        Line Capacity nylon (No.-m): 6-350-7-300-8-250
        Bearings: 22/1
        Max Winding Power (kg): 90 (100)
        JAFS standard speed (m/min): Hi230-Lo150
        Normal hoisting speed at 1kg load (m/min): Hi180(200)-Lo125(140)
        JAFS-Standard hoisting force-(kg): 22
        ",
        'product_id' => 4
        ],   
        [
        'features' => 
        "110m Baitcast Handle
        Aluminium Baitcast Handle
        Aluminium Body
        EVA Knob – Round Ball Knob
        ",
        'product_id' => 9
        ],  
        [
        'features' => 
        "Number of pieces: 10 pcs
        List price: 280pesos
        Size: 2 inches
        Total length: 5.4cm
        ",
        'product_id' => 10
        ], 
        [
        'features' => 
        "● A bullet-type sinker that matches well with worms and is ideal for use with Texas rigs
        ● Equipped with a protective tube to prevent line breaks
        ● Gun black finish that is familiar to all worm colours and does not give a sense of discomfort
        ● Uses a hard material (lead alloy) that increases sensitivity
        ",
        'product_id' => 17
        ], 
        [
        'features' => 
        "Mebao Accessory Box
        MBE1-103L (5 permanent vertical slots)
        Price: 65php cash 70php srp
        MBE1-203L (Empty Case)
        Price: 65php cash 70php srp
        MBE1-309L (Slotted Foam for Hooks)
        Price: 75php cash 80php srp
        ",
        'product_id' => 18
        ], 
    ]
    );
    }
}
