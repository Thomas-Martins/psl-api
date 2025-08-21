<?php

namespace Database\Factories;

use App\Models\Carrier;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $clientRoleId = Role::where('name', Role::CLIENT)->value('id') ?? 4;
        $clientUserId = User::where('role_id', $clientRoleId)
            ->inRandomOrder()
            ->value('id');
        $createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

        $carrierId = Carrier::inRandomOrder()->value('id');

        $status = $this->faker->randomElement(Order::STATUS_VALUES);
        $estimatedDeliveryDate = null;
        if ($status === Order::STATUS_PROCESSING) {
            $estimatedDeliveryDate = now()->addWeek();
        } elseif ($status === Order::STATUS_COMPLETED || $status === Order::STATUS_SHIPPED) {
            $estimatedDeliveryDate = now()->addDays(4);
        }

        return [
            'reference'   => $this->faker->unique()->bothify('ORD-######'),
            'user_id'     => $clientUserId ?? User::factory()->state(['role_id' => $clientRoleId]),
            'carrier_id'  => $carrierId,
            'status'      => $status,
            'estimated_delivery_date' => $estimatedDeliveryDate,
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
            'created_at'  => $createdAt,
            'updated_at'  => $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }
}
