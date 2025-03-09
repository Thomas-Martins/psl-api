<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'zipcode' => substr(fake()->postcode(), 0, 5),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'contact_person_firstname' => fake()->firstName(),
            'contact_person_lastname' => fake()->lastName(),
            'contact_person_phone' => fake()->phoneNumber(),
            'contact_person_email' => fake()->unique()->safeEmail(),
        ];
    }
}
