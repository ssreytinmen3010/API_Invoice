<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryFee;

class DeliveryFeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryFee::create(['amount' => 10]);
        DeliveryFee::create(['amount' => 20]);
    }
}