<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'name' => 'John Doe',
            'phone' => '1234567890',
            'alternative_phone' => '0987654321',
            'vat_customer_id' => 1,
            'address' => '123 Main St',
            'note' => 'Regular customer'
        ]);

        Customer::create([
            'name' => 'Jane Smith',
            'phone' => '1122334455',
            'alternative_phone' => null,
            'vat_customer_id' => 2,
            'address' => '456 Elm St',
            'note' => 'VIP customer'
        ]);
    }
}