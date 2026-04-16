<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
            \App\Models\Supplier::create([
                'name' => $faker->company,
                'information' => $faker->catchPhrase,
                'address' => $faker->address,
            ]);
        }
    }
}
