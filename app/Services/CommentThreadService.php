<?php

namespace App\Services;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CommentThreadService
{
    private const DUPLICATE_WINDOW_MINUTES = 5;

    /**
     * Create a root comment or flatten a reply into the thread's second level.
     */
    public function create(Post $post, User $user, string $content, ?Comment $replyTo): Comment
    {
        if ($replyTo !== null && $replyTo->post_id !== $post->id) {
            throw ValidationException::withMessages([
                'reply_to_id' => 'Bình luận được trả lời không thuộc bài viết này.',
            ]);
        }

        $latestComment = Comment::query()
            ->whereBelongsTo($post)
            ->whereBelongsTo($user)
            ->latest('created_at')
            ->latest('id')
            ->first(['content', 'created_at']);

        if ($latestComment?->content === $content && $latestComment->created_at->gte(now()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))) {
            throw ValidationException::withMessages([
                'content' => 'Bạn vừa gửi nội dung bình luận này. Vui lòng chờ trước khi gửi lại.',
            ]);
        }

        $rootId = $replyTo?->parent_id ?? $replyTo?->id;

        return $post->comments()->create([
            'user_id' => $user->id,
            'parent_id' => $rootId,
            'reply_to_id' => $replyTo?->id,
            'content' => $content,
            'status' => CommentStatus::Visible,
        ]);
    }
}
