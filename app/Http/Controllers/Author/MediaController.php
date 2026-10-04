<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\UploadPostMediaRequest;
use App\Services\OptimizedImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function upload(UploadPostMediaRequest $request, OptimizedImageService $images): JsonResponse
    {
        /** @var UploadedFile $image */
        $image = $request->file('image');
        $path = $images->storeScaled(
            $image,
            'posts/media',
            (int) config('images.post_media.max_width'),
            (int) config('images.post_media.max_height'),
        );

        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
