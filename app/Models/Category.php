<?php

namespace App\Models;

use App\Models\Concerns\InteractsWithMediaCollections;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property bool $is_active
 * @property int $sort_order
 */
#[Fillable(['parent_id', 'name', 'slug', 'description', 'image', 'is_active', 'sort_order'])]
class Category extends Model implements HasMedia
{
    /** @use HasFactory<CategoryFactory> */
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Parent category (self-referencing).
     *
     * @return BelongsTo<Category, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Child categories.
     *
     * @return HasMany<Category, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Products belonging to this category.
     *
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Options (variant dimensions) that apply to this category.
     *
     * @return BelongsToMany<ProductOption, $this>
     */
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class, 'category_product_option')
            ->withTimestamps();
    }

    /**
     * Attributes (specifications) that apply to this category.
     *
     * @return BelongsToMany<ProductAttribute, $this>
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class, 'category_product_attribute')
            ->withTimestamps();
    }
}
