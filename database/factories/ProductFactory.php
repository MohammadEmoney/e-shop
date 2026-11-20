<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = implode(' ', (array) fake()->words(2));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'price' => fake()->numberBetween(200000, 20000000),
            'compare_at_price' => null,
            'stock' => fake()->numberBetween(0, 100),
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'image' => null,
            'is_active' => true,
            'is_featured' => false,
            'embedding' => null,
            'search_text' => null,
        ];
    }
}
