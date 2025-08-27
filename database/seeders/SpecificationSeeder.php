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
            'specification' => "ATD drag technology reinforced by 30%, full-size drag button for stability
Bearings: 12 + 1 including 2 Mag Sealed and 10 CRBBs
Monocoque magnesium body and ZAION Air Rotor
Ultralight Aluminium Air Spool
CrossWrap line lay
Ultralight one-piece unscrewable and slit Air Handle in aluminium
Supplied in a neoprene sleeve with mesh sides"
        ]
    ]);
    }
}
