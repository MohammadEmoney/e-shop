<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => fake()->address(),
            'postal_code' => fake()->numerify('###########'),
            'phone' => fake()->numerify('09##########'),
            'is_default' => false,
        ];
    }
}
