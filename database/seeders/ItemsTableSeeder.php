<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create([
            'item_name' => 'Laptop',
            'item_code' => 'ITM001',
            'note' => 'High performance laptop',
            'image_id' => 1
        ]);

        Item::create([
            'item_name' => 'Mouse',
            'item_code' => 'ITM002',
            'note' => 'Wireless mouse',
            'image_id' => 2
        ]);
    }
}