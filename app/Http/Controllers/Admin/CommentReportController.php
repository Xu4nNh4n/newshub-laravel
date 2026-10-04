<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentReportAction;
use App\Enums\CommentReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HandleCommentReportRequest;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Services\CommentReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CommentReportController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Comment::class);
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(CommentReportStatus::class)],
        ]);
        $status = CommentReportStatus::tryFrom($filters['status'] ?? '') ?? CommentReportStatus::Pending;

        return view('admin.comment-reports.index', [
            'reports' => CommentReport::query()
                ->where('status', $status)
                ->with([
                    'reporter:id,name,email',
                    'comment' => fn ($query) => $query
                        ->withTrashed()
                        ->with('user:id,name'),
                ])
                ->latest('created_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'selectedStatus' => $status,
            'statuses' => CommentReportStatus::cases(),
        ]);
    }

    public function update(
        HandleCommentReportRequest $request,
        CommentReport $commentReport,
        CommentReportService $reports,
    ): RedirectResponse {
        $reports->handle(
            $commentReport,
            $request->user(),
            CommentReportAction::from($request->string('action')->toString()),
        );

        return back()->with('status', 'Đã xử lý báo cáo bình luận.');
    }
}
