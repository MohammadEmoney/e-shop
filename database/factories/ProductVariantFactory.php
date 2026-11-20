<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
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
            'name' => 'استاندارد',
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'price' => fake()->numberBetween(200000, 20000000),
            'stock' => fake()->numberBetween(0, 100),
            'attributes' => ['color' => 'مشکی', 'size' => 'M'],
        ];
    }
}
