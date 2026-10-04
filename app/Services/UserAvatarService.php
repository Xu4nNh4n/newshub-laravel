<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class UserAvatarService
{
    public function __construct(private readonly OptimizedImageService $images) {}

    /** Update profile fields and safely replace or remove the optional public avatar. */
    public function update(
        User $user,
        string $name,
        ?UploadedFile $avatar,
        ?string $email = null,
        bool $removeAvatar = false,
    ): void {
        $newAvatarPath = $avatar === null
            ? null
            : $this->images->storeSquare(
                $avatar,
                'users/avatars',
                (int) config('images.avatar.size'),
            );
        $oldAvatarPath = $user->avatar;
        $email ??= $user->email;
        $emailChanged = $email !== $user->email;
        $avatarPath = $newAvatarPath ?? ($removeAvatar ? null : $oldAvatarPath);

        try {
            $user->forceFill([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
                'avatar' => $avatarPath,
            ])->save();
        } catch (Throwable $exception) {
            if ($newAvatarPath !== null) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            throw $exception;
        }

        if ($oldAvatarPath !== null && ($newAvatarPath !== null || $removeAvatar)) {
            Storage::disk('public')->delete($oldAvatarPath);
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }
    }
}
