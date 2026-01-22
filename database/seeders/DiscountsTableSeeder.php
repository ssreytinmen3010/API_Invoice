<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Discount::create([
            'percent' => 10,
            'amount' => null
        ]);

        Discount::create([
            'percent' => null,
            'amount' => 50
        ]);
    }
}