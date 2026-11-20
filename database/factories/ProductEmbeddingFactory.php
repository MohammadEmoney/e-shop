<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductEmbedding;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductEmbedding>
 */
class ProductEmbeddingFactory extends Factory
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
            'embedding' => array_map(
                fn () => fake()->randomFloat(6, -1, 1),
                range(1, 1536)
            ),
            'model_name' => 'text-embedding-3-small',
        ];
    }
}
