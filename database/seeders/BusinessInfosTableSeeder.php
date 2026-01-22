<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessInfo;

class BusinessInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusinessInfo::create([
            'user_id' => 1,
            'name' => 'My Business',
            'phone' => '1234567890',
            'vat_customer_id' => 1,
            'address' => 'Business Address',
            'terms' => 'Terms and conditions',
            'image_id' => 1
        ]);
    }
}