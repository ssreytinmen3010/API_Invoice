<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoiceItem;

class InvoiceItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InvoiceItem::create([
            'invoice_id' => 1,
            'item_id' => 1,
            'unit_price' => 1000,
            'quantity' => 1
        ]);

        InvoiceItem::create([
            'invoice_id' => 1,
            'item_id' => 2,
            'unit_price' => 50,
            'quantity' => 2
        ]);
    }
}