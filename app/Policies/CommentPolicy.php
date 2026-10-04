<?php

namespace App\Policies;

use App\Enums\CommentStatus;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id && $comment->status === CommentStatus::Visible;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->role === UserRole::Admin || $comment->user_id === $user->id;
    }

    public function report(User $user, Comment $comment): bool
    {
        return $comment->user_id !== $user->id && $comment->status === CommentStatus::Visible;
    }
}
