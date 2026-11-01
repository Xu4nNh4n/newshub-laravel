<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostWorkflowService
{
    /** Move an editable article into the review queue and clear an old rejection. */
    public function submit(Post $post, User $actor): Post
    {
        return DB::transaction(function () use ($post): Post {
            $post->update([
                'status' => PostStatus::PendingReview,
                'rejection_reason' => null,
            ]);

            return $post->refresh();
        });
    }

    /** Approve an article for immediate or scheduled public visibility. */
    public function approve(Post $post, User $actor, ?CarbonInterface $publishedAt = null): Post
    {
        $publishedPost = $this->markPublished(
            $post,
            $publishedAt,
        );

        return $publishedPost;
    }

    /** Publish an administrator's own draft without entering the review queue. */
    public function publish(Post $post, User $actor, ?CarbonInterface $publishedAt = null): Post
    {
        return $this->markPublished(
            $post,
            $publishedAt,
        );
    }

    /** Return an article to its author with the required editorial reason. */
    public function reject(Post $post, User $actor, string $reason): Post
    {
        $rejectedPost = DB::transaction(function () use ($post, $reason): Post {
            $post->update([
                'status' => PostStatus::Rejected,
                'rejection_reason' => $reason,
                'published_at' => null,
            ]);

            return $post->refresh();
        });

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

            return $lockedPost->refresh();
        });
    }

    private function markPublished(
        Post $post,
        ?CarbonInterface $publishedAt,
    ): Post {
        return DB::transaction(function () use ($post, $publishedAt): Post {
            $post->update([
                'status' => PostStatus::Published,
                'published_at' => $publishedAt ?? now(),
                'rejection_reason' => null,
            ]);

            return $post->refresh();
        });
    }
}
