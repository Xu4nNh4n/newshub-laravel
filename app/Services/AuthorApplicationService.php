<?php

namespace App\Services;

use App\Enums\AuthorApplicationStatus;
use App\Enums\UserRole;
use App\Models\AuthorApplication;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthorApplicationService
{
    /**
     * @param  array{category_id:int, bio:string}  $attributes
     */
    public function submit(User $user, array $attributes, UploadedFile $cv): AuthorApplication
    {
        $cvPath = $cv->store('author-applications', 'local');

        if ($cvPath === false) {
            throw ValidationException::withMessages(['cv' => 'Không thể lưu tệp CV. Vui lòng thử lại.']);
        }

        try {
            return DB::transaction(function () use ($user, $attributes, $cvPath): AuthorApplication {
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
                    'cv_path' => $cvPath,
                    'pending_user_id' => $lockedUser->id,
                    'status' => AuthorApplicationStatus::Pending,
                ]);

                return $application;
            });
        } catch (QueryException $exception) {
            Storage::disk('local')->delete($cvPath);

            if ($user->authorApplications()->where('status', AuthorApplicationStatus::Pending)->exists()) {
                throw ValidationException::withMessages([
                    'application' => 'Bạn đang có một đơn ứng tuyển chờ duyệt.',
                ]);
            }

            throw $exception;
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($cvPath);

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

            return $lockedApplication->refresh();
        });
    }
}
