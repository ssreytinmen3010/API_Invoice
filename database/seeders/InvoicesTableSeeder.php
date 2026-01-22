<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::create([
            'invoice_date' => now(),
            'customer_id' => 1,
            'exchange_rate_id' => 1,
            'image_id' => 1,
            'discount_id' => 1,
            'delivery_fee_id' => 1,
            'vat_percent' => 10,
            'note' => 'Sample invoice'
        ]);
    }
}