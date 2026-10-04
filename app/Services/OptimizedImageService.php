<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use RuntimeException;

final class OptimizedImageService
{
    private readonly ImageManager $images;

    public function __construct()
    {
        $this->images = ImageManager::usingDriver(
            Driver::class,
            autoOrientation: true,
            decodeAnimation: false,
            strip: true,
        );
    }

    /** Scale an uploaded image within the bounds, encode it as WebP, and return its public-disk path. */
    public function storeScaled(UploadedFile $upload, string $directory, int $maxWidth, int $maxHeight): string
    {
        $image = $this->images
            ->decode($upload)
            ->scaleDown(width: $maxWidth, height: $maxHeight);

        return $this->store($image, $directory);
    }

    /** Centre-crop an uploaded image to a square without upscaling and return its public-disk path. */
    public function storeSquare(UploadedFile $upload, string $directory, int $size): string
    {
        $image = $this->images
            ->decode($upload)
            ->coverDown($size, $size);

        return $this->store($image, $directory);
    }

    /** Encode a prepared image without metadata and persist exactly one WebP file. */
    private function store(ImageInterface $image, string $directory): string
    {
        $path = trim($directory, '/').'/'.Str::uuid().'.webp';
        $quality = (int) config('images.webp_quality');
        $contents = (string) $image->encodeUsingFormat(Format::WEBP, quality: $quality, strip: true);
        $disk = Storage::disk('public');

        if (! $disk->put($path, $contents)) {
            $disk->delete($path);

            throw new RuntimeException('Không thể lưu ảnh đã tối ưu.');
        }

        return $path;
    }
}
