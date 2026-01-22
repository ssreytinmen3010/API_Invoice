<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\File;

class FileSeeder extends Seeder
{
    public function run(): void
    {
        // Example 1: Local sample files in storage/app/public/uploads
        File::create([
            'file_path' => 'uploads/cutie.jpg',
            'file_type' => 'jpg'
        ]);

        File::create([
            'file_path' => 'uploads/milo.jpg',
            'file_type' => 'jpg'
        ]);

     
    }
}
