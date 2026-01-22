<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // $this->call(UsersTableSeeder::class);
        // $this->call(VatCustomersTableSeeder::class);
        // $this->call(CustomersTableSeeder::class);
        // $this->call(ItemsTableSeeder::class);
        // $this->call(DiscountsTableSeeder::class);
        // $this->call(DeliveryFeesTableSeeder::class);
        // $this->call(ExchangeRatesTableSeeder::class);
        // $this->call(InvoicesTableSeeder::class);
        // $this->call(InvoiceItemsTableSeeder::class);
        // $this->call(BusinessInfosTableSeeder::class);
        // $this->call(PaymentsTableSeeder::class);
        // $this->call(SignaturesTableSeeder::class);
        $this->call(FileSeeder::class);

    }
}
