<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);
        $this->call(PersonalAccessClientSeeder::class);
        $this->call(StoresSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(CarriersSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
