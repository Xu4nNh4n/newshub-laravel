<?php

namespace App\Http\Controllers;

use App\Enums\CommentReportReason;
use App\Http\Requests\Comment\StoreCommentReportRequest;
use App\Models\Comment;
use App\Services\CommentReportService;
use Illuminate\Http\RedirectResponse;

class CommentReportController extends Controller
{
    public function __invoke(
        StoreCommentReportRequest $request,
        Comment $comment,
        CommentReportService $reports,
    ): RedirectResponse {
        $reports->report(
            $comment,
            $request->user(),
            CommentReportReason::from($request->string('reason')->toString()),
            $request->string('description')->toString() ?: null,
        );

        return back()->with('status', 'Đã gửi báo cáo để quản trị viên xem xét.');
    }
}
