<?php

namespace App\Services;

use App\Enums\PostRequestStatus;
use App\Enums\PostRequestType;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\PostRequest;
use App\Models\User;
use App\Notifications\PostRequestHandledNotification;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostRequestService
{
    /**
     * @param  array{type:string, priority:string, reason:string, notes?:string|null}  $attributes
     */
    public function submit(Post $post, User $author, array $attributes): PostRequest
    {
        try {
            return DB::transaction(function () use ($post, $author, $attributes): PostRequest {
                $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);

                if (
                    $lockedPost->author_id !== $author->id
                    || ! in_array($author->role, [UserRole::Author, UserRole::Admin], true)
                    || $lockedPost->status !== PostStatus::Published
                ) {
                    throw ValidationException::withMessages([
                        'post' => 'Chỉ có thể gửi yêu cầu cho bài viết đã xuất bản của bạn.',
                    ]);
                }

                if ($lockedPost->requests()->where('status', PostRequestStatus::Pending)->exists()) {
                    throw ValidationException::withMessages([
                        'post' => 'Bài viết này đang có một yêu cầu chờ Ban biên tập xử lý.',
                    ]);
                }

                $postRequest = $lockedPost->requests()->create([
                    ...$attributes,
                    'pending_post_id' => $lockedPost->id,
                    'author_id' => $author->id,
                    'notes' => (string) ($attributes['notes'] ?? ''),
                    'status' => PostRequestStatus::Pending,
                ]);

                $this->log(
                    $author,
                    $postRequest,
                    'post_request.created',
                    'Tác giả đã gửi yêu cầu '.$postRequest->type->value.' cho bài viết.',
                );

                return $postRequest;
            });
        } catch (QueryException $exception) {
            if ($post->requests()->where('status', PostRequestStatus::Pending)->exists()) {
                throw ValidationException::withMessages([
                    'post' => 'Bài viết này đang có một yêu cầu chờ Ban biên tập xử lý.',
                ]);
            }

            throw $exception;
        }
    }

    public function handle(
        PostRequest $postRequest,
        User $admin,
        PostRequestStatus $status,
        ?string $adminNotes,
    ): PostRequest {
        return DB::transaction(function () use ($postRequest, $admin, $status, $adminNotes): PostRequest {
            $lockedRequest = PostRequest::query()->lockForUpdate()->findOrFail($postRequest->id);

            if ($admin->role !== UserRole::Admin || $lockedRequest->status !== PostRequestStatus::Pending) {
                throw ValidationException::withMessages([
                    'request' => 'Yêu cầu này không còn ở trạng thái chờ xử lý.',
                ]);
            }

            $lockedPost = Post::query()->lockForUpdate()->findOrFail($lockedRequest->post_id);

            if ($status === PostRequestStatus::Approved) {
                $lockedPost->update($lockedRequest->type === PostRequestType::Removal
                    ? [
                        'status' => PostStatus::Hidden,
                        'is_featured' => false,
                    ]
                    : [
                        'status' => PostStatus::Draft,
                        'published_at' => null,
                        'rejection_reason' => null,
                        'is_featured' => false,
                    ]);
            }

            $lockedRequest->update([
                'pending_post_id' => null,
                'status' => $status,
                'admin_notes' => $adminNotes,
                'handled_by' => $admin->id,
                'handled_at' => now(),
            ]);

            $this->log(
                $admin,
                $lockedRequest,
                'post_request.'.$status->value,
                $adminNotes ?: ($status === PostRequestStatus::Approved
                    ? 'Ban biên tập đã phê duyệt yêu cầu bài viết.'
                    : 'Ban biên tập đã từ chối yêu cầu bài viết.'),
            );

            $lockedRequest->author?->notify(new PostRequestHandledNotification(
                $lockedRequest,
                $status,
                $adminNotes,
            ));

            return $lockedRequest->refresh();
        });
    }

    public function cancel(PostRequest $postRequest, User $author): PostRequest
    {
        return DB::transaction(function () use ($postRequest, $author): PostRequest {
            $lockedRequest = PostRequest::query()->lockForUpdate()->findOrFail($postRequest->id);

            if ($lockedRequest->author_id !== $author->id || $lockedRequest->status !== PostRequestStatus::Pending) {
                throw ValidationException::withMessages([
                    'request' => 'Yêu cầu này không còn ở trạng thái chờ xử lý hoặc bạn không có quyền hủy.',
                ]);
            }

            $lockedRequest->update([
                'pending_post_id' => null,
                'status' => PostRequestStatus::Cancelled,
                'handled_at' => now(),
            ]);

            $this->log(
                $author,
                $lockedRequest,
                'post_request.cancelled',
                'Tác giả đã hủy yêu cầu '.$lockedRequest->type->value.' cho bài viết.',
            );

            return $lockedRequest->refresh();
        });
    }

    private function log(User $actor, PostRequest $postRequest, string $action, string $description): void
    {
        ActivityLog::query()->create([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => PostRequest::class,
            'subject_id' => $postRequest->id,
            'description' => $description,
        ]);
    }
}
