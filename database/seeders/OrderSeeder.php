<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrdersProduct;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();


        Order::factory(20)->create()->each(function ($order) use ($products) {
            $productsForOrder = $products->random(rand(1, 5));
            foreach ($productsForOrder as $product) {
                OrdersProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'freeze_price' => $product->price,
                ]);
            }
        });
    }
}
