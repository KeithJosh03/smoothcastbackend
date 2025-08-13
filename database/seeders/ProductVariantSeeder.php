<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantSeeder extends Seeder {
    public function run(): void {
        DB::table('product_variants')->insert([
            [
                'product_id' => 1,
                'full_model_name' => "Daiwa 18 EXIST G LT 3000 CXH",
                'price' => 27000.00
            ]
        ]);
    }
}
