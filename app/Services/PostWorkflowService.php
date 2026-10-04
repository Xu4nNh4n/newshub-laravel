<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostStatusUpdatedNotification;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostWorkflowService
{
    /** Move an editable article into the review queue and clear an old rejection. */
    public function submit(Post $post, User $actor): Post
    {
        return DB::transaction(function () use ($post, $actor): Post {
            $post->update([
                'status' => PostStatus::PendingReview,
                'rejection_reason' => null,
            ]);
            $this->log($actor, $post, 'post.submitted', 'Bài viết được gửi duyệt.');

            return $post->refresh();
        });
    }

    /** Approve an article for immediate or scheduled public visibility. */
    public function approve(Post $post, User $actor, ?CarbonInterface $publishedAt = null): Post
    {
        $publishedPost = $this->markPublished(
            $post,
            $actor,
            $publishedAt,
            'post.approved',
            'Bài viết được duyệt để xuất bản.',
        );

        if ($post->author_id !== $actor->id) {
            $post->author?->notify(new PostStatusUpdatedNotification($post, 'approved'));
        }

        return $publishedPost;
    }

    /** Publish an administrator's own draft without entering the review queue. */
    public function publish(Post $post, User $actor, ?CarbonInterface $publishedAt = null): Post
    {
        return $this->markPublished(
            $post,
            $actor,
            $publishedAt,
            'post.published',
            'Quản trị viên đã xuất bản bài viết trực tiếp.',
        );
    }

    /** Return an article to its author with the required editorial reason. */
    public function reject(Post $post, User $actor, string $reason): Post
    {
        $rejectedPost = DB::transaction(function () use ($post, $actor, $reason): Post {
            $post->update([
                'status' => PostStatus::Rejected,
                'rejection_reason' => $reason,
                'published_at' => null,
            ]);
            $this->log($actor, $post, 'post.rejected', $reason);

            return $post->refresh();
        });

        if ($post->author_id !== $actor->id) {
            $post->author?->notify(new PostStatusUpdatedNotification($post, 'rejected', $reason));
        }

        return $rejectedPost;
    }

    /** Return a pending article to its owner's editable drafts. */
    public function withdraw(Post $post, User $actor): Post
    {
        return DB::transaction(function () use ($post, $actor): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);

            if ($lockedPost->author_id !== $actor->id || $lockedPost->status !== PostStatus::PendingReview) {
                throw ValidationException::withMessages([
                    'post' => 'Chỉ có thể rút bài viết đang chờ duyệt của bạn.',
                ]);
            }

            $lockedPost->update([
                'status' => PostStatus::Draft,
                'rejection_reason' => null,
            ]);
            $this->log($actor, $lockedPost, 'post.withdrawn', 'Tác giả đã rút bài chờ duyệt về bản nháp.');

            return $lockedPost->refresh();
        });
    }

    private function log(User $actor, Post $post, string $action, string $description): void
    {
        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => $description,
        ]);
    }

    private function markPublished(
        Post $post,
        User $actor,
        ?CarbonInterface $publishedAt,
        string $action,
        string $description,
    ): Post {
        return DB::transaction(function () use ($post, $actor, $publishedAt, $action, $description): Post {
            $post->update([
                'status' => PostStatus::Published,
                'published_at' => $publishedAt ?? now(),
                'rejection_reason' => null,
            ]);
            $this->log($actor, $post, $action, $description);

            return $post->refresh();
        });
    }
}
