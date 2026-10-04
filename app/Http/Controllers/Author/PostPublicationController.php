<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostPublicationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Post $post, PostWorkflowService $workflow): RedirectResponse
    {
        Gate::authorize('publish', $post);
        $workflow->publish($post, $request->user());

        return redirect()->route('author.posts.index')->with('status', 'Đã xuất bản bài viết thành công.');
    }
}
