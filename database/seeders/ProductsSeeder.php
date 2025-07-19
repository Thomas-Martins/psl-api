<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        if ($categories->isEmpty() || $suppliers->isEmpty()) {
            $this->command->error('Il faut créer des catégories et des fournisseurs avant de lancer le seeder des produits.');
            return;
        }

        Product::factory(100)->make()->each(function ($product) use ($categories, $suppliers) {
            $product->category_id = $categories->random()->id;
            $product->supplier_id = $suppliers->random()->id;
            $product->save();
        });
    }
}
