<?php

namespace App\Models\Concerns;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Shared media collection definitions for models that use
 * Spatie\MediaLibrary. Provides the "images", "videos" and "files"
 * collections plus a WebP conversion with responsive images.
 *
 * Each consuming model must declare its own registerMediaCollections()
 * (which simply calls configureMediaCollections()) to satisfy the
 * Spatie\MediaLibrary\InteractsWithMedia contract without a trait collision.
 */
trait InteractsWithMediaCollections
{
    /**
     * Configure the three media collections used across the admin panel.
     */
    protected function configureMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/svg+xml',
                'image/avif',
            ])
            ->useDisk('public')
            ->withResponsiveImages()
            ->registerMediaConversions(function (?Media $media = null): void {
                $this->addMediaConversion('webp')
                    ->withResponsiveImages()
                    ->nonQueued()
                    ->quality(80)
                    ->format('webp');
            });

        $this->addMediaCollection('main_image')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/avif',
            ])
            ->useDisk('public')
            ->withResponsiveImages()
            ->registerMediaConversions(function (?Media $media = null): void {
                $this->addMediaConversion('webp')
                    ->withResponsiveImages()
                    ->nonQueued()
                    ->quality(80)
                    ->format('webp');
            });

        $this->addMediaCollection('videos')
            ->acceptsMimeTypes([
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/quicktime',
            ])
            ->useDisk('public');

        $this->addMediaCollection('files')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/zip',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ])
            ->useDisk('public');
    }

    /**
     * The URL of the first image (or null). Convenience accessor for views.
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('images');

        return $media?->getUrl();
    }

    /**
     * A small thumbnail URL for the first image, if a conversion exists.
     */
    public function getPrimaryImageThumbUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('images');

        return $media?->hasGeneratedConversion('webp')
            ? $media->getUrl('webp')
            : $media?->getUrl();
    }
}
