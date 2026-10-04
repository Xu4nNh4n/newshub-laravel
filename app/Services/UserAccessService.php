<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class UserAccessService
{
    /** Update a non-admin account and audit only actual access changes. */
    public function update(User $target, User $admin, UserRole $role, UserStatus $status): bool
    {
        $originalRole = $target->role;
        $originalStatus = $target->status;

        $target->fill(['role' => $role, 'status' => $status]);

        if (! $target->isDirty()) {
            return false;
        }

        $target->save();

        ActivityLog::query()->create([
            'user_id' => $admin->id,
            'action' => 'user.access-updated',
            'subject_type' => User::class,
            'subject_id' => $target->id,
            'description' => sprintf(
                'Vai trò %s → %s; trạng thái %s → %s.',
                $originalRole->value,
                $role->value,
                $originalStatus->value,
                $status->value,
            ),
        ]);

        return true;
    }

    /** Send a password reset link to a non-admin account and record an audit log. */
    public function sendResetLink(User $target, User $admin): bool
    {
        Password::sendResetLink(['email' => $target->email]);

        ActivityLog::query()->create([
            'user_id' => $admin->id,
            'action' => 'user.password-reset-sent',
            'subject_type' => User::class,
            'subject_id' => $target->id,
            'description' => "Quản trị viên đã gửi liên kết đặt lại mật khẩu đến {$target->email}.",
        ]);

        return true;
    }
}
