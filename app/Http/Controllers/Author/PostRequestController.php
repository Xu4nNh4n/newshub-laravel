<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StorePostWorkflowRequest;
use App\Models\Post;
use App\Models\PostRequest;
use App\Services\PostRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PostRequestController extends Controller
{
    public function store(
        StorePostWorkflowRequest $request,
        Post $post,
        PostRequestService $postRequests,
    ): RedirectResponse {
        $postRequests->submit($post, $request->user(), $request->validated());

        return back()->with('status', 'Đã gửi yêu cầu tới Ban biên tập.');
    }

    public function destroy(
        Post $post,
        PostRequest $postRequest,
        PostRequestService $postRequests,
    ): RedirectResponse {
        if ($postRequest->post_id !== $post->id) {
            abort(404);
        }

        Gate::authorize('cancel', $postRequest);

        $postRequests->cancel($postRequest, request()->user());

        return back()->with('status', 'Đã hủy yêu cầu bài viết thành công.');
    }
}
