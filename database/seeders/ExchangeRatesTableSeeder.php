<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExchangeRate;

class ExchangeRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeRate::create([
            'currency' => 'USD',
            'rate' => 1.0,
            'is_active' => true
        ]);

        ExchangeRate::create([
            'currency' => 'EUR',
            'rate' => 0.85,
            'is_active' => false
        ]);
    }
}