<?php

namespace Database\Factories;

use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\ProductMeasurement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductMeasurement>
 */
class ProductMeasurementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'measurement_unit_id' => MeasurementUnit::factory(),
            'min_quantity' => 0.5,
            'max_quantity' => 100,
            'step' => 0.5,
        ];
    }
}
