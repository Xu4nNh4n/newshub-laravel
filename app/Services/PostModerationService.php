<?php

namespace App\Services;

use App\Enums\PostModerationAction;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostModerationService
{
    /** Apply one validated editorial action atomically. */
    public function apply(Post $post, User $actor, PostModerationAction $action): Post
    {
        return DB::transaction(function () use ($post, $actor, $action): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);

            return $this->applyLocked($lockedPost, $actor, $action);
        });
    }

    private function applyLocked(Post $post, User $actor, PostModerationAction $action): Post
    {
        $post->update($this->attributesFor($post, $action));

        return $post->refresh();
    }

    /** @return array{status?: PostStatus, published_at?: mixed} */
    private function attributesFor(Post $post, PostModerationAction $action): array
    {
        return match ($action) {
            PostModerationAction::Hide => $this->requireStatus($post, [PostStatus::Published], [
                'status' => PostStatus::Hidden,
            ]),
            PostModerationAction::Archive => $this->requireStatus($post, [PostStatus::Published, PostStatus::Hidden], [
                'status' => PostStatus::Archived,
            ]),
            PostModerationAction::Restore => $this->requireStatus($post, [PostStatus::Hidden, PostStatus::Archived], [
                'status' => PostStatus::Published,
                'published_at' => $post->published_at ?? now(),
            ]),
        };
    }

    /**
     * @param  list<PostStatus>  $allowedStatuses
     * @param  array{status?: PostStatus, published_at?: mixed}  $attributes
     * @return array{status?: PostStatus, published_at?: mixed}
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
}
