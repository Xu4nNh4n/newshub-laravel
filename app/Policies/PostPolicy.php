<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::Author, UserRole::Admin], true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return $user->role === UserRole::Admin || $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::Author, UserRole::Admin], true);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->role === UserRole::Admin || (
            $post->author_id === $user->id
            && in_array($post->status, [PostStatus::Draft, PostStatus::Rejected], true)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }

    public function submit(User $user, Post $post): bool
    {
        return in_array($user->role, [UserRole::Author, UserRole::Admin], true)
            && $post->author_id === $user->id
            && in_array($post->status, [PostStatus::Draft, PostStatus::Rejected], true);
    }

    public function publish(User $user, Post $post): bool
    {
        return $user->role === UserRole::Admin
            && $post->author_id === $user->id
            && in_array($post->status, [PostStatus::Draft, PostStatus::Rejected], true);
    }

    public function review(User $user, Post $post): bool
    {
        return $user->role === UserRole::Admin && $post->status === PostStatus::PendingReview;
    }

    public function withdraw(User $user, Post $post): bool
    {
        return in_array($user->role, [UserRole::Author, UserRole::Admin], true)
            && $post->author_id === $user->id
            && $post->status === PostStatus::PendingReview;
    }

    public function requestChange(User $user, Post $post): bool
    {
        return in_array($user->role, [UserRole::Author, UserRole::Admin], true)
            && $post->author_id === $user->id
            && $post->status === PostStatus::Published;
    }
}
