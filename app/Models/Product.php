<?php

namespace App\Models;

use App\Models\Concerns\InteractsWithMediaCollections;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $short_description
 * @property int $price
 * @property int|null $compare_at_price
 * @property int $stock
 * @property string|null $sku
 * @property string|null $image
 * @property bool $is_active
 * @property bool $is_featured
 * @property array<float>|null $embedding
 * @property string|null $search_text
 */
#[Fillable([
    'category_id',
    'name',
    'slug',
    'description',
    'short_description',
    'price',
    'compare_at_price',
    'stock',
    'sku',
    'image',
    'is_active',
    'is_featured',
    'embedding',
    'search_text',
])]
class Product extends Model implements HasMedia
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use InteractsWithMediaCollections;

    /**
     * Register the media collections (images, videos, files) for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->configureMediaCollections();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_at_price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            // AI vector embeddings stored as a JSON array.
            'embedding' => 'array',
        ];
    }

    /**
     * The category this product belongs to.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Product variants (e.g. Red / Large).
     *
     * @return HasMany<ProductVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * The attribute values (dimensions) this product uses to build variants.
     *
     * @return BelongsToMany<ProductAttributeValue, $this>
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'product_attribute_value_product')
            ->withTimestamps();
    }

    /**
     * The attributes (e.g. color, size) this product uses.
     *
     * @return BelongsToMany<ProductAttribute, $this>
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_attribute_value_product');
    }

    /**
     * Per-product measurement configuration (for uncountable products).
     *
     * @return HasMany<ProductMeasurement, $this>
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(ProductMeasurement::class);
    }

    /**
     * Whether the product is sold by measurement (not countable).
     */
    public function isMeasurable(): bool
    {
        return $this->measurements()->exists();
    }

    /**
     * Orders this product appears in (via order_items pivot).
     *
     * @return BelongsToMany<Order, $this, Pivot>
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['product_variant_id', 'product_name', 'variant_name', 'quantity', 'unit_price', 'total_price'])
            ->withTimestamps();
    }

    /**
     * The most recent AI embedding for this product.
     *
     * @return HasOne<ProductEmbedding, $this>
     */
    public function embeddingModel(): HasOne
    {
        return $this->hasOne(ProductEmbedding::class)->latestOfMany();
    }

    /**
     * All AI embeddings for this product (normalized variant).
     *
     * @return HasMany<ProductEmbedding, $this>
     */
    public function embeddings(): HasMany
    {
        return $this->hasMany(ProductEmbedding::class);
    }

    /**
     * Denormalized, text-optimized searchable text for AI embedding generation.
     * Concatenates name, short description, description and category name.
     *
     * @return Attribute<string, never>
     */
    public function searchableText(): Attribute
    {
        return Attribute::make(
            get: fn (): string => implode(' ', array_filter([
                $this->name,
                $this->short_description,
                $this->description,
                $this->category?->name,
            ])),
        );
    }

    /**
     * Whether this product has an active embedding stored.
     */
    public function hasEmbedding(): bool
    {
        return ! empty($this->embedding) || $this->embeddingModel()->exists();
    }
}
