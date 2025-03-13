<?php

namespace Database\Factories;

use App\Models\Carrier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carrier>
 */
class CarrierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'zipcode' => substr(fake()->postcode(), 0, 5),
            'contact_person_firstname' => $this->faker->name,
            'contact_person_lastname' => $this->faker->name,
            'contact_person_phone' => $this->faker->phoneNumber,
            'contact_person_email' => $this->faker->unique()->safeEmail,
        ];
    }
}
