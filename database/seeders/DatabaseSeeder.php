<?php

namespace Database\Seeders;

use App\Models\User;
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
            SetupSeeder::class,
            PackageSeeder::class,
            InclusionSeeder::class,
            SetupImageSeeder::class,
            ProductDiscountSeeder::class
        ]);
    }
}
