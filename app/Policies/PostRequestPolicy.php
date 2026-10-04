<?php

namespace App\Policies;

use App\Enums\PostRequestStatus;
use App\Enums\UserRole;
use App\Models\PostRequest;
use App\Models\User;

class PostRequestPolicy
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
    public function view(User $user, PostRequest $postRequest): bool
    {
        return $user->role === UserRole::Admin || $postRequest->author_id === $user->id;
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
    public function update(User $user, PostRequest $postRequest): bool
    {
        return $user->role === UserRole::Admin
            && $postRequest->status === PostRequestStatus::Pending;
    }

    /**
     * Determine whether the user can cancel the request.
     */
    public function cancel(User $user, PostRequest $postRequest): bool
    {
        return $postRequest->author_id === $user->id
            && $postRequest->status === PostRequestStatus::Pending;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PostRequest $postRequest): bool
    {
        return $this->cancel($user, $postRequest);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PostRequest $postRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PostRequest $postRequest): bool
    {
        return false;
    }
}
