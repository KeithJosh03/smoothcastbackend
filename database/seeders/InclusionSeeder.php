<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InclusionSeeder extends Seeder {
    public function run(): void {
        DB::table('inclusions')->insert([
        'setup_id' => 1,
        'inclusion' => 
        '10 pcs snap
        ug duha ka tomboy'
        ]);
    }
}
