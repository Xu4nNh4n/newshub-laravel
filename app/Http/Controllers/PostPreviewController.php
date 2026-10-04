<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\PostContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostPreviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Post $post, PostContentSanitizer $contentSanitizer): View
    {
        Gate::authorize('view', $post);
        $post->load(['author:id,name', 'category:id,name,slug', 'tags:id,name,slug']);

        return view('news.preview', [
            'post' => $post,
            'thumbnailUrl' => $post->thumbnail === null ? null : Storage::disk('public')->url($post->thumbnail),
            'safeContent' => $contentSanitizer->sanitize($post->content),
        ]);
    }
}
