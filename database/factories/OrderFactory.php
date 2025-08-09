<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $clientUser = User::where('role_id', 4)
            ->inRandomOrder()
            ->first();

        return [
            'reference' => $this->faker->unique()->bothify('ORD-####'),
            'user_id' => $clientUser ? $clientUser->id : 1,
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
