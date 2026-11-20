<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
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
            'order_number' => 'ORD-'.Str::upper(Str::random(10)),
            'status' => Order::STATUS_PENDING,
            'subtotal' => fake()->numberBetween(500000, 20000000),
            'discount' => 0,
            'shipping_cost' => 45000,
            'total' => 500000,
            'payment_method' => null,
            'shipping_address' => null,
            'notes' => null,
            'paid_at' => null,
        ];
    }
}
