<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetupSeeder extends Seeder {
    public function run(): void {
        DB::table('setups')->insert([
            [
            'setup_name' => "Medium Light",
            'code_name' => "ML1",
            'description' => "Choose between Aerolite ARL 652ULXS-SD OR ARL 682LXS-SD rod and 18 Legalis 1000S, 18 Legalis 2000S-XH or 18Legalis 2500S-XH (JDM) reel and get it only for the price of 4400php cash. A very special promo offer for those who are looking for quality light gaming set at a lower price.

Features
The Aerolite rods are carbon made rods that has a fast taper and a solid rods tip while 18 legalis reels are Japan domestic model reels that were made specially for shore casting concentrating more on xtra ultralight setups hence the airbail, airspool and abs drag system",
            'start_date' => '2025-10-07',
            'end_date' => '2025-12-25',
            'value_discount' => 1000.00,
            'type_discount' => 'Unit'
            ],
            [
            'setup_name' => "Large Light",
            'code_name' => "LL1",
            'description' => "Choose between Aerolite ARL 652ULXS-SD OR ARL 682LXS-SD rod and 18 Legalis 1000S, 18 Legalis 2000S-XH or 18Legalis 2500S-XH (JDM) reel and get it only for the price of 4400php cash. A very special promo offer for those who are looking for quality light gaming set at a lower price.

Features
The Aerolite rods are carbon made rods that has a fast taper and a solid rods tip while 18 legalis reels are Japan domestic model reels that were made specially for shore casting concentrating more on xtra ultralight setups hence the airbail, airspool and abs drag system",
            'start_date' => '2025-10-07',
            'end_date' => '2025-12-25',
            'value_discount' => 2000.00,
            'type_discount' => 'Unit'
            ],
            
        ]);
    }
}

