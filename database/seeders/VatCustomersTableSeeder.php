<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VatCustomer;

class VatCustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VatCustomer::create(['percent' => 0]);
        VatCustomer::create(['percent' => 10]);
        VatCustomer::create(['percent' => 20]);
    }
}