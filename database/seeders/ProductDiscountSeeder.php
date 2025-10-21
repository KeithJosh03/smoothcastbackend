<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductDiscountSeeder extends Seeder {
    public function run(): void {
        DB::table('product_discounts')->insert([
            [
            'variant_id' => 1,
            'startDate' => '2025-10-09',
            'endDate' => '2025-12-30',
            'discount_type' => 'Unit',
            'discount_value' => 999.00
            ],
            [
            'variant_id' => 4,
            'startDate' => '2025-10-09',
            'endDate' => '2025-12-30',
            'discount_type' => 'Percent',
            'discount_value' => 20
            ],
            [
            'variant_id' => 25,
            'startDate' => '2025-10-09',
            'endDate' => '2025-12-30',
            'discount_type' => 'Percent',
            'discount_value' => 10
            ]
        ]);
    }
}
