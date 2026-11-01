<?php

namespace App\Http\Controllers;

use App\Enums\CommentModerationAction;
use App\Enums\UserRole;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentModerationService;
use App\Services\CommentThreadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post, CommentThreadService $threads): RedirectResponse
    {
        abort_unless(Post::query()->publiclyVisible()->whereKey($post)->exists(), 404);

        $replyTo = $request->filled('reply_to_id')
            ? Comment::query()->findOrFail($request->integer('reply_to_id'))
            : null;

        $comment = $threads->create($post, $request->user(), $request->string('content')->toString(), $replyTo);

        return back()->with('status', 'Đã đăng bình luận.');
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $comment->update(['content' => $request->string('content')->toString()]);

        return back()->with('status', 'Đã cập nhật bình luận.');
    }

    public function destroy(Comment $comment, CommentModerationService $moderation): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        if (request()->user()->role === UserRole::Admin) {
            $moderation->apply($comment, request()->user(), CommentModerationAction::Delete);
        } else {
            $comment->delete();
        }

        return back()->with('status', 'Đã xóa bình luận.');
    }
}
