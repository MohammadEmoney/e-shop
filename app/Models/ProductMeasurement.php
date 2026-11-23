<?php

namespace App\Models;

use Database\Factories\ProductMeasurementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property int $measurement_unit_id
 * @property string $min_quantity
 * @property string|null $max_quantity
 * @property string $step
 */
#[Fillable(['product_id', 'measurement_unit_id', 'min_quantity', 'max_quantity', 'step'])]
class ProductMeasurement extends Model
{
    /** @use HasFactory<ProductMeasurementFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_quantity' => 'decimal:4',
            'max_quantity' => 'decimal:4',
            'step' => 'decimal:4',
        ];
    }

    /**
     * The product this measurement belongs to.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The unit of measure for this product.
     *
     * @return BelongsTo<MeasurementUnit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit_id');
    }
}
