<?php

namespace App\Services;

use App\Enums\CommentModerationAction;
use App\Enums\CommentStatus;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommentModerationService
{
    /** Apply an admin moderation action and record it in the same transaction. */
    public function apply(Comment $comment, User $admin, CommentModerationAction $action): void
    {
        DB::transaction(function () use ($comment, $admin, $action): void {
            $lockedComment = Comment::query()->withTrashed()->lockForUpdate()->findOrFail($comment->id);

            match ($action) {
                CommentModerationAction::Hide => $this->hide($lockedComment),
                CommentModerationAction::Restore => $this->restore($lockedComment),
                CommentModerationAction::Delete => $this->delete($lockedComment),
            };

            ActivityLog::query()->create([
                'user_id' => $admin->id,
                'action' => 'comment.'.$action->value,
                'subject_type' => Comment::class,
                'subject_id' => $lockedComment->id,
                'description' => 'Admin kiểm duyệt bình luận.',
            ]);
        });
    }

    private function hide(Comment $comment): void
    {
        $this->rejectWhen($comment->trashed() || $comment->status === CommentStatus::Hidden);
        $comment->update(['status' => CommentStatus::Hidden]);
    }

    private function restore(Comment $comment): void
    {
        $this->rejectWhen($comment->trashed() || $comment->status !== CommentStatus::Hidden);
        $comment->update(['status' => CommentStatus::Visible]);
    }

    private function delete(Comment $comment): void
    {
        $this->rejectWhen($comment->trashed());
        $comment->delete();
    }

    private function rejectWhen(bool $condition): void
    {
        if ($condition) {
            throw ValidationException::withMessages([
                'action' => 'Thao tác không phù hợp với trạng thái hiện tại của bình luận.',
            ]);
        }
    }
}
