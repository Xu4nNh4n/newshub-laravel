<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostSubmissionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Post $post, PostWorkflowService $workflow): RedirectResponse
    {
        Gate::authorize('submit', $post);
        $workflow->submit($post, request()->user());

        return redirect()->route('author.posts.index')->with('status', 'Đã gửi bài để duyệt.');
    }

    public function withdraw(Request $request, Post $post, PostWorkflowService $workflow): RedirectResponse
    {
        Gate::authorize('withdraw', $post);
        $workflow->withdraw($post, $request->user());

        return redirect()->route('author.posts.edit', $post)->with('status', 'Đã rút bài về bản nháp.');
    }
}
