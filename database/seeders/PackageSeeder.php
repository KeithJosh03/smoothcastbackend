<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder {
    public function run(): void {
     DB::table('packages')->insert([
        [
        'variant_id' => 1,
        'setup_id' => 1,
        ],
        [
        'variant_id' => 24,
        'setup_id' => 1,
        ],
        [
        'variant_id' => 18,
        'setup_id' => 1,
        ],
     ]);
    }
}
