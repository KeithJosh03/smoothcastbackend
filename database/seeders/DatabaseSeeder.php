<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            BrandSeeder::class,
            CategorySeeder::class,
            CategoryTypesSeeder::class,
            ProductSeeder::class,
            SpecificationSeeder::class,
            FeatureSeeder::class,
            ProductVariantSeeder::class,
            ProductImageSeeder::class,
        ]);
    }
}
