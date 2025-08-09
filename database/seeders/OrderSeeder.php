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
        // Charge uniquement les champs nécessaires
        $products = Product::query()->select('id', 'price')->get();
        if ($products->isEmpty()) {
            $this->command?->warn('No products found. Skipping order-product attachments.');
            return;
        }

        Order::factory(20)->create()->each(function ($order) use ($products) {
            $max = min(5, $products->count());
            if ($max === 0) {
                return;
            }
            $count = random_int(1, $max);
            $productsForOrder = $products->random($count);
            foreach ($productsForOrder as $product) {
                OrdersProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'freeze_price' => $product->price,
                ]);
            }
            // Recalcule et persiste le total_price basé sur les produits associés
            $order->update([
                'total_price' => $order->calculateTotal(),
            ]);
        });
    }
}
