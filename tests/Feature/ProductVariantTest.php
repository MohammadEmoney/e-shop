<?php

use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Database\Seeders\ProductVariantSeeder;

/**
 * Verification for the product variant / attribute / measurement system.
 */
it('seeds and resolves measurement units, attributes and variants', function () {
    Product::factory()->count(3)->create();
    $this->seed(ProductVariantSeeder::class);

    expect(ProductAttribute::count())->toBeGreaterThanOrEqual(3);
    expect(MeasurementUnit::count())->toBeGreaterThanOrEqual(5);

    // Units have the correct types.
    $meter = MeasurementUnit::query()->where('name', 'متر')->first();
    expect($meter)->not->toBeNull()
        ->and($meter->type)->toBe(MeasurementUnit::TYPE_LENGTH)
        ->and($meter->toBase(1))->toBe(1.0);

    $cm = MeasurementUnit::query()->where('name', 'سانتی‌متر')->first();
    expect($cm)->not->toBeNull()
        ->and($cm->base_unit_id)->toBe($meter->id)
        ->and($cm->toBase(100))->toBe(1.0);
});

it('resolves product -> attributeValues and variant -> attributeValues', function () {
    Product::factory()->count(3)->create();
    $this->seed(ProductVariantSeeder::class);

    $product = Product::query()->whereHas('variants')->first();
    expect($product)->not->toBeNull();

    // Product has attribute dimensions attached.
    expect($product->attributeValues()->count())->toBeGreaterThan(0);

    // A variant resolves its dimension values, each pointing back to an attribute.
    $variant = $product->variants()->whereHas('attributeValues')->first();
    expect($variant)->not->toBeNull();

    $dimensions = $variant->attributeValues->map(
        fn (ProductAttributeValue $av) => $av->attribute->name.':'.$av->value
    );

    expect($dimensions->count())->toBeGreaterThan(0);
});

it('supports measurable (uncountable) products', function () {
    Product::factory()->count(3)->create();
    $this->seed(ProductVariantSeeder::class);

    $measurable = Product::query()->whereHas('measurements')->first();
    expect($measurable)->not->toBeNull()
        ->and($measurable->isMeasurable())->toBeTrue();

    $measurement = $measurable->measurements->first();
    expect($measurement)->not->toBeNull()
        ->and($measurement->unit)->not->toBeNull();
});
