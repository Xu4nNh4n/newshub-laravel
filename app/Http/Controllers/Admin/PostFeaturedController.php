<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostModerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PostFeaturedController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Post $post, PostModerationService $moderation): RedirectResponse
    {
        Gate::authorize('update', $post);

        $post = $moderation->toggleFeatured($post, request()->user());

        return back()->with(
            'status',
            $post->is_featured
                ? 'Đã ghim bài viết làm Tiêu điểm trang chủ.'
                : 'Đã bỏ ghim tiêu điểm bài viết.',
        );
    }
}
