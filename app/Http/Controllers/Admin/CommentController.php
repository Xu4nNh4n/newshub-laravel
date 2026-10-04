<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentModerationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModerateCommentRequest;
use App\Models\Comment;
use App\Services\CommentModerationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Comment::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', Rule::in(['visible', 'hidden', 'deleted'])],
        ]);

        return view('admin.comments.index', [
            'comments' => Comment::query()
                ->withTrashed()
                ->when(($filters['state'] ?? null) === 'deleted', fn (Builder $query): Builder => $query->onlyTrashed())
                ->when(in_array($filters['state'] ?? null, ['visible', 'hidden'], true), function (Builder $query) use ($filters): Builder {
                    return $query->whereNull('deleted_at')->where('status', $filters['state']);
                })
                ->when($filters['q'] ?? null, fn (Builder $query, string $keyword): Builder => $query->where('content', 'like', "%{$keyword}%"))
                ->with(['user:id,name,email', 'post:id,title,slug'])
                ->latest('created_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $filters,
            'states' => ['visible' => 'Đang hiển thị', 'hidden' => 'Đã ẩn', 'deleted' => 'Đã xóa'],
        ]);
    }

    public function update(
        ModerateCommentRequest $request,
        Comment $comment,
        CommentModerationService $moderation,
    ): RedirectResponse {
        $moderation->apply(
            $comment,
            $request->user(),
            CommentModerationAction::from($request->string('action')->toString()),
        );

        return back()->with('status', 'Đã cập nhật bình luận.');
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Comment::class);

        $response = new StreamedResponse(function (): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Bài viết', 'Người bình luận', 'Nội dung', 'Trạng thái', 'Ngày tạo']);

            Comment::query()
                ->withTrashed()
                ->with(['user:id,name', 'post:id,title'])
                ->latest('id')
                ->chunk(200, function ($comments) use ($handle): void {
                    foreach ($comments as $comment) {
                        $state = $comment->trashed() ? 'Đã xóa' : ($comment->status === 'visible' ? 'Hiển thị' : 'Đã ẩn');
                        fputcsv($handle, [
                            $comment->id,
                            $comment->post?->title ?? '',
                            $comment->user?->name ?? '',
                            $comment->content,
                            $state,
                            $comment->created_at->format('d/m/Y H:i'),
                        ]);
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="danh-sach-binh-luan-'.now()->format('Ymd_His').'.csv"');

        return $response;
    }
}
