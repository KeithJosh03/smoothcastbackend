<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {

    public function run(): void {
        DB::table('admins')->insert([
            'name' => 'Smooth Cast',
            'email' => 'smoothcast123@gmail.com',
            'password' => Hash::make('smoothcast123') 
        ]);
    }
}
