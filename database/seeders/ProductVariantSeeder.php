<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductMeasurement;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed measurement units, product attributes, options, variants and measurements.
     */
    public function run(): void
    {
        $this->seedMeasurementUnits();
        $this->seedAttributes();
        $this->seedOptions();

        // Attach attributes + variants to some products, and measurements to others.
        $this->seedVariantsOnProducts();
        $this->seedMeasurementsOnProducts();
    }

    /**
     * Create measurement units (countable, weight, length).
     */
    protected function seedMeasurementUnits(): void
    {
        $kg = MeasurementUnit::query()->firstOrCreate(
            ['name' => 'کیلوگرم', 'type' => MeasurementUnit::TYPE_WEIGHT],
            ['symbol' => 'kg', 'factor' => 1, 'is_active' => true],
        );
        MeasurementUnit::query()->firstOrCreate(
            ['name' => 'گرم', 'type' => MeasurementUnit::TYPE_WEIGHT],
            ['symbol' => 'g', 'base_unit_id' => $kg->id, 'factor' => 0.001, 'is_active' => true],
        );

        $m = MeasurementUnit::query()->firstOrCreate(
            ['name' => 'متر', 'type' => MeasurementUnit::TYPE_LENGTH],
            ['symbol' => 'm', 'factor' => 1, 'is_active' => true],
        );
        MeasurementUnit::query()->firstOrCreate(
            ['name' => 'سانتی‌متر', 'type' => MeasurementUnit::TYPE_LENGTH],
            ['symbol' => 'cm', 'base_unit_id' => $m->id, 'factor' => 0.01, 'is_active' => true],
        );

        MeasurementUnit::query()->firstOrCreate(
            ['name' => 'عدد', 'type' => MeasurementUnit::TYPE_COUNTABLE],
            ['symbol' => 'عدد', 'factor' => 1, 'is_active' => true],
        );
    }

    /**
     * Create product attributes and their allowed values.
     */
    protected function seedAttributes(): void
    {
        $color = $this->makeAttribute('رنگ', 'color', ProductAttribute::TYPE_COLOR, ['قرمز', 'مشکی', 'سفید', 'آبی']);
        $size = $this->makeAttribute('سایز', 'size', ProductAttribute::TYPE_SELECT, ['S', 'M', 'L', 'XL']);
        $this->makeAttribute('جنس', 'material', ProductAttribute::TYPE_SELECT, ['پنبه', 'کتان', 'پلی‌استر']);
    }

    /**
     * Create an attribute (if missing) with its values.
     *
     * @param  list<string>  $values
     */
    protected function makeAttribute(string $name, string $slug, string $type, array $values): ProductAttribute
    {
        $attr = ProductAttribute::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'type' => $type, 'is_filterable' => true],
        );

        foreach ($values as $order => $value) {
            ProductAttributeValue::query()->firstOrCreate(
                ['product_attribute_id' => $attr->id, 'value' => $value],
                ['sort_order' => $order],
            );
        }

        return $attr;
    }

    /**
     * Create product options (variant dimensions such as size and color) and
     * attach them to the clothing categories so the product form can build
     * variants for those categories.
     */
    protected function seedOptions(): void
    {
        $size = $this->makeOption('سایز', 'size', ProductOption::TYPE_SELECT, [
            'کوچک (S)' => 's',
            'متوسط (M)' => 'm',
            'بزرگ (L)' => 'l',
        ]);
        $color = $this->makeOption('رنگ', 'color', ProductOption::TYPE_COLOR, [
            'آبی' => 'blue',
            'سفید' => 'white',
            'مشکی' => 'black',
        ]);

        $categorySlugs = ['pierahan-mardane', 'shalvar-mardane', 'manto', 'shal-o-roosari'];
        foreach ($categorySlugs as $slug) {
            $category = Category::query()->where('slug', $slug)->first();
            if ($category) {
                $category->options()->syncWithoutDetaching([$size->id, $color->id]);
            }
        }
    }

    /**
     * Create an option (if missing) with its values.
     *
     * @param  array<string, string>  $values  map of value -> slug
     */
    protected function makeOption(string $name, string $slug, string $type, array $values): ProductOption
    {
        $option = ProductOption::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'type' => $type, 'is_active' => true],
        );

        foreach ($values as $value => $valueSlug) {
            ProductOptionValue::query()->firstOrCreate(
                ['product_option_id' => $option->id, 'value' => $value],
                ['slug' => $valueSlug],
            );
        }

        return $option;
    }

    /**
     * Attach color/size dimensions and generate variants for some products.
     */
    protected function seedVariantsOnProducts(): void
    {
        $products = Product::query()->limit(4)->get();

        foreach ($products as $i => $product) {
            $color = ProductAttribute::query()->where('slug', 'color')->first();
            $size = ProductAttribute::query()->where('slug', 'size')->first();
            if (! $color || ! $size) {
                continue;
            }

            $colorValues = $color->values->take(2);
            $sizeValues = $size->values->take(2);

            // Attach the dimensions to the product.
            $dimensions = $colorValues->merge($sizeValues);
            $product->attributeValues()->sync($dimensions->pluck('id'));

            // Create a couple of variants per product.
            $variantN = 0;
            foreach ($colorValues as $cv) {
                foreach ($sizeValues as $sv) {
                    $name = "{$cv->value} / {$sv->value}";
                    $variant = ProductVariant::query()->updateOrCreate(
                        ['product_id' => $product->id, 'name' => $name],
                        [
                            'sku' => $product->sku.'-V'.($i + 1).($variantN + 1),
                            'price' => $product->price,
                            'stock' => rand(3, 20),
                            'is_active' => true,
                        ],
                    );
                    $variant->attributeValues()->sync([$cv->id, $sv->id]);
                    $variantN++;
                }
            }
        }
    }

    /**
     * Assign a measurement (e.g. fabric sold by the meter) to a couple of products.
     */
    protected function seedMeasurementsOnProducts(): void
    {
        $meter = MeasurementUnit::query()->where('name', 'متر')->first();
        $kg = MeasurementUnit::query()->where('name', 'کیلوگرم')->first();
        if (! $meter || ! $kg) {
            return;
        }

        $fabric = Product::query()->where('name', 'like', '%کتان%')->orWhere('name', 'like', '%پارچه%')->first();
        if ($fabric) {
            ProductMeasurement::query()->firstOrCreate(
                ['product_id' => $fabric->id, 'measurement_unit_id' => $meter->id],
                ['min_quantity' => 0.5, 'max_quantity' => 50, 'step' => 0.5],
            );
        }

        $firstProduct = Product::query()->first();
        if ($firstProduct) {
            ProductMeasurement::query()->firstOrCreate(
                ['product_id' => $firstProduct->id, 'measurement_unit_id' => $kg->id],
                ['min_quantity' => 0.1, 'max_quantity' => 20, 'step' => 0.1],
            );
        }
    }
}
