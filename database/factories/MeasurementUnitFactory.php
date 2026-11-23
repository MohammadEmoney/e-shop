<?php

namespace Database\Factories;

use App\Models\MeasurementUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeasurementUnit>
 */
class MeasurementUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = implode(' ', (array) fake()->words(1));

        return [
            'name' => $name,
            'symbol' => strtoupper(substr($name, 0, 1)),
            'type' => MeasurementUnit::TYPE_COUNTABLE,
            'base_unit_id' => null,
            'factor' => 1,
            'is_active' => true,
        ];
    }
}
