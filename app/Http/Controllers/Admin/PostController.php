<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostModerationAction;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModeratePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\PostModerationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(PostStatus::class)],
            'category_id' => ['nullable', 'integer', Rule::exists(Category::class, 'id')->whereNull('deleted_at')],
            'author_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
        ]);

        return view('admin.posts.index', [
            'posts' => Post::query()
                ->when($filters['q'] ?? null, function (Builder $query, string $keyword): void {
                    $query->where(function (Builder $query) use ($keyword): void {
                        $query->where('title', 'like', "%{$keyword}%")
                            ->orWhere('slug', 'like', "%{$keyword}%");
                    });
                })
                ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
                ->when($filters['category_id'] ?? null, fn (Builder $query, int|string $categoryId): Builder => $query->where('category_id', $categoryId))
                ->when($filters['author_id'] ?? null, fn (Builder $query, int|string $authorId): Builder => $query->where('author_id', $authorId))
                ->with(['author:id,name', 'category:id,name'])
                ->latest('created_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $filters,
            'statuses' => PostStatus::cases(),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'authors' => User::query()
                ->whereIn('role', [UserRole::Author, UserRole::Admin])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(
        ModeratePostRequest $request,
        Post $post,
        PostModerationService $moderation,
    ): RedirectResponse {
        $action = PostModerationAction::from($request->string('action')->toString());
        $moderation->apply($post, $request->user(), $action);

        return back()->with('status', 'Đã cập nhật trạng thái bài viết.');
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', Post::class);

        $response = new StreamedResponse(function (): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Tiêu đề', 'Chuyên mục', 'Tác giả', 'Trạng thái', 'Nổi bật', 'Lượt xem', 'Ngày xuất bản', 'Ngày tạo']);

            Post::query()
                ->with(['author:id,name', 'category:id,name'])
                ->latest('id')
                ->chunk(200, function ($posts) use ($handle): void {
                    foreach ($posts as $post) {
                        fputcsv($handle, [
                            $post->id,
                            $post->title,
                            $post->category?->name ?? '',
                            $post->author?->name ?? '',
                            $post->status->value,
                            $post->is_featured ? 'Có' : 'Không',
                            $post->view_count,
                            $post->published_at ? $post->published_at->format('d/m/Y H:i') : '',
                            $post->created_at->format('d/m/Y H:i'),
                        ]);
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="danh-sach-bai-viet-'.now()->format('Ymd_His').'.csv"');

        return $response;
    }
}
