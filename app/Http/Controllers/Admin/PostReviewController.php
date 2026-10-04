<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectPostRequest;
use App\Models\Post;
use App\Services\PostContentSanitizer;
use App\Services\PostWorkflowService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PostReviewController extends Controller
{
    public function index(PostContentSanitizer $contentSanitizer): View
    {
        Gate::authorize('viewAny', Post::class);

        $posts = Post::query()
            ->where('status', PostStatus::PendingReview)
            ->with(['author:id,name', 'category:id,name'])
            ->oldest('created_at')
            ->oldest('id')
            ->paginate(15);

        return view('admin.posts.review', [
            'posts' => $posts,
            'sanitizedContents' => $posts->getCollection()
                ->mapWithKeys(fn (Post $post): array => [
                    $post->id => $contentSanitizer->sanitize($post->content),
                ]),
        ]);
    }

    /** Approve now or schedule visibility using an optional publication time. */
    public function approve(Request $request, Post $post, PostWorkflowService $workflow): RedirectResponse
    {
        Gate::authorize('review', $post);
        $validated = $request->validate(['published_at' => ['nullable', 'date']]);
        $publishedAt = isset($validated['published_at']) ? CarbonImmutable::parse($validated['published_at']) : null;
        $workflow->approve($post, $request->user(), $publishedAt);

        return back()->with('status', 'Đã duyệt bài viết.');
    }

    public function reject(RejectPostRequest $request, Post $post, PostWorkflowService $workflow): RedirectResponse
    {
        $workflow->reject($post, $request->user(), $request->string('reason')->toString());

        return back()->with('status', 'Đã từ chối bài viết.');
    }
}
