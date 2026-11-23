<?php

namespace App\Models;

use Database\Factories\MeasurementUnitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property string $type
 * @property int|null $base_unit_id
 * @property string $factor
 * @property bool $is_active
 */
#[Fillable(['name', 'symbol', 'type', 'base_unit_id', 'factor', 'is_active'])]
class MeasurementUnit extends Model
{
    /** @use HasFactory<MeasurementUnitFactory> */
    use HasFactory;

    public const TYPE_COUNTABLE = 'countable';

    public const TYPE_WEIGHT = 'weight';

    public const TYPE_LENGTH = 'length';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'factor' => 'decimal:6',
        ];
    }

    /**
     * The base unit this unit converts to (e.g. meter is base of cm).
     *
     * @return BelongsTo<MeasurementUnit, $this>
     */
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'base_unit_id');
    }

    /**
     * Units that convert into this base unit.
     *
     * @return HasMany<MeasurementUnit, $this>
     */
    public function subUnits(): HasMany
    {
        return $this->hasMany(MeasurementUnit::class, 'base_unit_id');
    }

    /**
     * Convert a quantity in this unit to its base unit value.
     */
    public function toBase(float $quantity): float
    {
        return (float) $quantity * (float) $this->factor;
    }
}
