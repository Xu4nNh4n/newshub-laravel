<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class PostThumbnailService
{
    public function __construct(private readonly OptimizedImageService $images) {}

    /** Resize and encode a validated thumbnail as one WebP file, then return its relative path. */
    public function store(UploadedFile $thumbnail): string
    {
        return $this->images->storeScaled(
            $thumbnail,
            'posts/thumbnails',
            (int) config('images.post_thumbnail.max_width'),
            (int) config('images.post_thumbnail.max_height'),
        );
    }

    public function delete(?string $path): void
    {
        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }
}
