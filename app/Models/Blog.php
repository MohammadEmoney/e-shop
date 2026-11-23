<?php

namespace App\Models;

use App\Models\Concerns\InteractsWithMediaCollections;
use Database\Factories\BlogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $content
 * @property bool $is_published
 * @property Carbon|null $published_at
 */
#[Fillable(['title', 'slug', 'excerpt', 'content', 'is_published', 'published_at'])]
class Blog extends Model implements HasMedia
{
    /** @use HasFactory<BlogFactory> */
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
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
