<?php

namespace App\Services;

use App\Models\ProductOptionValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Generates product variant combinations from the cartesian product of the
 * selected option values (e.g. size {S,M,L} x color {blue,white} -> 6 variants).
 *
 * Cleaner, stateless alternative to an inline trait method: pure data in,
 * variant drafts out. Prices/stock default to the product's main values and
 * can be edited per variant afterwards.
 */
class VariantGenerator
{
    /**
     * Build variant drafts from grouped option values.
     *
     * @param  array<int|string, array<int, ProductOptionValue>>  $groupedValues
     *                                                                            e.g. ['size' => [S,M,L], 'color' => [blue,white]]
     * @param  int  $defaultPrice  base price copied to every variant
     * @param  int  $defaultStock  default stock copied to every variant
     * @param  string  $productSlug  the parent product slug used in variant slugs
     * @return list<array{name: string, slug: string, price: int, stock: int, is_active: bool, option_value_ids: array<int>}>
     */
    public function generate(array $groupedValues, int $defaultPrice, int $defaultStock, string $productSlug): array
    {
        $cartesian = Arr::crossJoin(...array_values($groupedValues));

        $variants = [];

        foreach ($cartesian as $combo) {
            $optionValueIds = array_map(fn (ProductOptionValue $v) => $v->id, $combo);

            // Human-readable name: "قرمز / M"
            $name = implode(' / ', array_map(fn (ProductOptionValue $v) => $v->value, $combo));

            // Unique slug: "red-m-girls-shirt" (option slugs joined + product slug)
            $slug = $this->makeVariantSlug(array_values($combo), $productSlug);

            $variants[] = [
                'name' => $name,
                'slug' => $slug,
                'price' => $defaultPrice,
                'stock' => $defaultStock,
                'is_active' => true,
                'option_value_ids' => $optionValueIds,
            ];
        }

        return $variants;
    }

    /**
     * Build a unique variant slug from its option values and the product slug.
     *
     * @param  array<int, ProductOptionValue>  $comboValues
     */
    private function makeVariantSlug(array $comboValues, string $productSlug): string
    {
        $parts = array_map(fn (ProductOptionValue $v) => $v->slug ?: Str::slug($v->value), $comboValues);

        $base = implode('-', $parts);

        // e.g. "small-blue" + "girls-shirt" => "small-blue-girls-shirt"
        return Str::slug($base.'-'.$productSlug, '-', 'en');
    }
}
