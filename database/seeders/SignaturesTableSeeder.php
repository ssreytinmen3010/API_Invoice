<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Signature;

class SignaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Signature::create([
            'image_id' => 1
        ]);
    }
}