<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class UserAccessService
{
    /** Update a non-admin account only when access values change. */
    public function update(User $target, User $admin, UserRole $role, UserStatus $status): bool
    {
        $target->fill(['role' => $role, 'status' => $status]);

        if (! $target->isDirty()) {
            return false;
        }

        $target->save();

        return true;
    }

    /** Send a password reset link to a non-admin account. */
    public function sendResetLink(User $target, User $admin): bool
    {
        Password::sendResetLink(['email' => $target->email]);

        return true;
    }
}
