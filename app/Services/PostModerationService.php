<?php

namespace App\Services;

use App\Enums\PostModerationAction;
use App\Enums\PostStatus;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostModerationService
{
    /** Apply one validated editorial action and keep its audit record atomic. */
    public function apply(Post $post, User $actor, PostModerationAction $action): Post
    {
        return DB::transaction(function () use ($post, $actor, $action): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);

            return $this->applyLocked($lockedPost, $actor, $action);
        });
    }

    /** Atomically toggle the featured state using the latest stored value. */
    public function toggleFeatured(Post $post, User $actor): Post
    {
        return DB::transaction(function () use ($post, $actor): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);
            $action = $lockedPost->is_featured
                ? PostModerationAction::Unfeature
                : PostModerationAction::Feature;

            return $this->applyLocked($lockedPost, $actor, $action);
        });
    }

    private function applyLocked(Post $post, User $actor, PostModerationAction $action): Post
    {
        $post->update($this->attributesFor($post, $action));

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => $this->logActionFor($action),
            'subject_type' => Post::class,
            'subject_id' => $post->id,
            'description' => $this->descriptionFor($post, $action),
        ]);

        return $post->refresh();
    }

    /** @return array{status?: PostStatus, is_featured?: bool, published_at?: mixed} */
    private function attributesFor(Post $post, PostModerationAction $action): array
    {
        return match ($action) {
            PostModerationAction::Hide => $this->requireStatus($post, [PostStatus::Published], [
                'status' => PostStatus::Hidden,
                'is_featured' => false,
            ]),
            PostModerationAction::Archive => $this->requireStatus($post, [PostStatus::Published, PostStatus::Hidden], [
                'status' => PostStatus::Archived,
                'is_featured' => false,
            ]),
            PostModerationAction::Restore => $this->requireStatus($post, [PostStatus::Hidden, PostStatus::Archived], [
                'status' => PostStatus::Published,
                'published_at' => $post->published_at ?? now(),
            ]),
            PostModerationAction::Feature => $this->requireStatus($post, [PostStatus::Published], ['is_featured' => true]),
            PostModerationAction::Unfeature => $this->requireStatus($post, [PostStatus::Published], ['is_featured' => false]),
        };
    }

    /**
     * @param  list<PostStatus>  $allowedStatuses
     * @param  array{status?: PostStatus, is_featured?: bool, published_at?: mixed}  $attributes
     * @return array{status?: PostStatus, is_featured?: bool, published_at?: mixed}
     */
    private function requireStatus(Post $post, array $allowedStatuses, array $attributes): array
    {
        if (! in_array($post->status, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'action' => 'Thao tác này không phù hợp với trạng thái hiện tại của bài viết.',
            ]);
        }

        return $attributes;
    }

    private function logActionFor(PostModerationAction $action): string
    {
        return match ($action) {
            PostModerationAction::Hide => 'post.hidden',
            PostModerationAction::Archive => 'post.archived',
            PostModerationAction::Restore => 'post.restored',
            PostModerationAction::Feature => 'post.featured',
            PostModerationAction::Unfeature => 'post.unfeatured',
        };
    }

    private function descriptionFor(Post $post, PostModerationAction $action): string
    {
        return match ($action) {
            PostModerationAction::Hide => 'Bài viết đã bị ẩn khỏi trang công khai.',
            PostModerationAction::Archive => 'Bài viết đã được lưu trữ.',
            PostModerationAction::Restore => 'Bài viết đã được khôi phục để xuất bản.',
            PostModerationAction::Feature => "Ghim tiêu điểm bài viết: {$post->title}",
            PostModerationAction::Unfeature => "Hủy tiêu điểm bài viết: {$post->title}",
        };
    }
}
