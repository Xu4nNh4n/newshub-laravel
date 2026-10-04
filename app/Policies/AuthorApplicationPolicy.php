<?php

namespace App\Policies;

use App\Enums\AuthorApplicationStatus;
use App\Enums\UserRole;
use App\Models\AuthorApplication;
use App\Models\User;

class AuthorApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuthorApplication $authorApplication): bool
    {
        return $user->role === UserRole::Admin || $authorApplication->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::User;
    }

    /** Determine whether an administrator can approve a pending application. */
    public function approve(User $user, AuthorApplication $authorApplication): bool
    {
        return $user->role === UserRole::Admin
            && $authorApplication->status === AuthorApplicationStatus::Pending;
    }

    /** Determine whether an administrator can reject a pending application. */
    public function reject(User $user, AuthorApplication $authorApplication): bool
    {
        return $this->approve($user, $authorApplication);
    }
}
