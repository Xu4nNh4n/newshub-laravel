<?php

namespace App\Services;

use App\Enums\CommentReportAction;
use App\Enums\CommentReportReason;
use App\Enums\CommentReportStatus;
use App\Enums\CommentStatus;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommentReportService
{
    /** Create one immutable report per reporter and comment. */
    public function report(Comment $comment, User $reporter, CommentReportReason $reason, ?string $description): CommentReport
    {
        try {
            return CommentReport::query()->create([
                'comment_id' => $comment->id,
                'reporter_id' => $reporter->id,
                'reason' => $reason,
                'description' => $description,
                'status' => CommentReportStatus::Pending,
            ]);
        } catch (QueryException $exception) {
            if (CommentReport::query()->whereBelongsTo($comment)->whereBelongsTo($reporter, 'reporter')->exists()) {
                throw ValidationException::withMessages([
                    'reason' => 'Bạn đã báo cáo bình luận này trước đó.',
                ]);
            }

            throw $exception;
        }
    }

    /** Apply the moderation action and close all pending reports for the comment atomically. */
    public function handle(CommentReport $report, User $admin, CommentReportAction $action): void
    {
        DB::transaction(function () use ($report, $admin, $action): void {
            $lockedReport = CommentReport::query()->lockForUpdate()->findOrFail($report->id);

            if ($lockedReport->status !== CommentReportStatus::Pending) {
                throw ValidationException::withMessages(['action' => 'Báo cáo này đã được xử lý.']);
            }

            $comment = Comment::query()->withTrashed()->lockForUpdate()->findOrFail($lockedReport->comment_id);

            if ($action === CommentReportAction::Hide && ! $comment->trashed()) {
                $comment->update(['status' => CommentStatus::Hidden]);
            }

            if ($action === CommentReportAction::Delete && ! $comment->trashed()) {
                $comment->delete();
            }

            $reportStatus = $action === CommentReportAction::Dismiss
                ? CommentReportStatus::Dismissed
                : CommentReportStatus::Resolved;

            CommentReport::query()
                ->whereBelongsTo($comment)
                ->where('status', CommentReportStatus::Pending)
                ->update([
                    'status' => $reportStatus,
                    'handled_by' => $admin->id,
                    'handled_at' => now(),
                ]);

            ActivityLog::query()->create([
                'user_id' => $admin->id,
                'action' => 'comment-report.'.$action->value,
                'subject_type' => Comment::class,
                'subject_id' => $comment->id,
                'description' => 'Đã xử lý báo cáo bình luận.',
            ]);
        });
    }
}
