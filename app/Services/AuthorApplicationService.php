<?php

namespace App\Services;

use App\Enums\AuthorApplicationStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\AuthorApplication;
use App\Models\User;
use App\Notifications\AuthorApplicationStatusNotification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthorApplicationService
{
    /**
     * @param  array{category_id:int, bio:string, sample_title:string, sample_content:string}  $attributes
     */
    public function submit(User $user, array $attributes): AuthorApplication
    {
        try {
            return DB::transaction(function () use ($user, $attributes): AuthorApplication {
                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

                if ($lockedUser->role !== UserRole::User) {
                    throw ValidationException::withMessages([
                        'application' => 'Chỉ tài khoản độc giả mới có thể nộp đơn ứng tuyển tác giả.',
                    ]);
                }

                if ($lockedUser->authorApplications()->where('status', AuthorApplicationStatus::Pending)->exists()) {
                    throw ValidationException::withMessages([
                        'application' => 'Bạn đang có một đơn ứng tuyển chờ duyệt.',
                    ]);
                }

                $application = $lockedUser->authorApplications()->create([
                    ...$attributes,
                    'pending_user_id' => $lockedUser->id,
                    'status' => AuthorApplicationStatus::Pending,
                ]);

                ActivityLog::query()->create([
                    'user_id' => $lockedUser->id,
                    'action' => 'application.submitted',
                    'subject_type' => AuthorApplication::class,
                    'subject_id' => $application->id,
                    'description' => 'Nộp đơn ứng tuyển làm Tác giả / Cộng tác viên tòa soạn.',
                ]);

                return $application;
            });
        } catch (QueryException $exception) {
            if ($user->authorApplications()->where('status', AuthorApplicationStatus::Pending)->exists()) {
                throw ValidationException::withMessages([
                    'application' => 'Bạn đang có một đơn ứng tuyển chờ duyệt.',
                ]);
            }

            throw $exception;
        }
    }

    public function approve(AuthorApplication $application, User $admin): AuthorApplication
    {
        return $this->review($application, $admin, AuthorApplicationStatus::Approved);
    }

    public function reject(AuthorApplication $application, User $admin, string $reason): AuthorApplication
    {
        return $this->review($application, $admin, AuthorApplicationStatus::Rejected, $reason);
    }

    private function review(
        AuthorApplication $application,
        User $admin,
        AuthorApplicationStatus $status,
        ?string $rejectionReason = null,
    ): AuthorApplication {
        return DB::transaction(function () use ($application, $admin, $status, $rejectionReason): AuthorApplication {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($application->user_id);
            $lockedApplication = AuthorApplication::query()->lockForUpdate()->findOrFail($application->id);

            if ($lockedApplication->status !== AuthorApplicationStatus::Pending) {
                throw ValidationException::withMessages([
                    'application' => 'Đơn ứng tuyển này đã được xử lý.',
                ]);
            }

            $lockedApplication->update([
                'pending_user_id' => null,
                'status' => $status,
                'rejection_reason' => $status === AuthorApplicationStatus::Rejected ? $rejectionReason : null,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            if ($status === AuthorApplicationStatus::Approved && $lockedUser->role === UserRole::User) {
                $lockedUser->update(['role' => UserRole::Author]);
            }

            ActivityLog::query()->create([
                'user_id' => $admin->id,
                'action' => 'author_application.'.$status->value,
                'subject_type' => AuthorApplication::class,
                'subject_id' => $lockedApplication->id,
                'description' => $status === AuthorApplicationStatus::Approved
                    ? 'Đơn ứng tuyển tác giả đã được phê duyệt.'
                    : $rejectionReason,
            ]);

            $lockedUser->notify(new AuthorApplicationStatusNotification(
                $lockedApplication,
                $status->value,
                $rejectionReason,
            ));

            return $lockedApplication->refresh();
        });
    }
}
