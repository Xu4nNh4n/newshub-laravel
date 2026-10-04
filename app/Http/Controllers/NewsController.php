<?php

namespace App\Http\Controllers;

use App\Enums\CommentReportReason;
use App\Enums\CommentStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Post;
use App\Services\PostContentSanitizer;
use App\Services\PostViewRecorder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:255', Rule::exists('categories', 'slug')->whereNull('deleted_at')],
            'tag' => ['nullable', 'string', 'max:255', Rule::exists('tags', 'slug')],
            'sort' => ['nullable', Rule::in(['latest', 'popular'])],
        ]);

        $selectedCategory = isset($filters['category'])
            ? Category::query()
                ->where('slug', $filters['category'])
                ->with('children:id,parent_id')
                ->first()
            : null;
        $categoryIds = $selectedCategory?->selfAndChildIds();

        $posts = Post::query()
            ->publiclyVisible()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->when($filters['q'] ?? null, function ($query, string $keyword): void {
                $query->where(function ($query) use ($keyword): void {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('summary', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%");
                });
            })
            ->when($categoryIds !== null, fn (Builder $query): Builder => $query->whereIn('category_id', $categoryIds))
            ->when($filters['tag'] ?? null, fn ($query, string $slug) => $query->whereHas('tags', fn ($query) => $query->where('slug', $slug)))
            ->when(
                ($filters['sort'] ?? 'latest') === 'popular',
                fn ($query) => $query->orderByDesc('view_count')->orderByDesc('id'),
                fn ($query) => $query->orderByDesc('published_at')->orderByDesc('id'),
            )
            ->paginate(12)
            ->withQueryString();

        return view('news.index', [
            'posts' => $posts,
            'filters' => $filters,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    /** Show only currently public posts, record a deduplicated view, and load related articles. */
    public function show(
        Request $request,
        string $slug,
        PostViewRecorder $views,
        PostContentSanitizer $contentSanitizer,
    ): View {
        $post = Post::query()
            ->publiclyVisible()
            ->where('slug', $slug)
            ->with(['author:id,name,avatar', 'category:id,name,slug', 'tags:id,name,slug'])
            ->firstOrFail();

        $ipHash = $request->ip() === null ? null : hash_hmac('sha256', $request->ip(), config('app.key'));
        $sessionIdentity = $request->session()->get('news_viewer_id');

        if (! is_string($sessionIdentity)) {
            $sessionIdentity = (string) Str::uuid();
            $request->session()->put('news_viewer_id', $sessionIdentity);
        }

        $views->record($post, $request->user(), $sessionIdentity, $ipHash);

        $tagIds = $post->tags->modelKeys();
        $relatedPosts = Post::query()
            ->publiclyVisible()
            ->whereKeyNot($post->id)
            ->where(function (Builder $query) use ($post, $tagIds): void {
                $query->where('category_id', $post->category_id)
                    ->when($tagIds !== [], fn (Builder $query): Builder => $query->orWhereHas(
                        'tags',
                        fn (Builder $query): Builder => $query->whereKey($tagIds),
                    ));
            })
            ->withCount(['tags as matching_tags_count' => fn (Builder $query): Builder => $query->whereKey($tagIds)])
            ->with(['author:id,name', 'category:id,name,slug'])
            ->orderByDesc('matching_tags_count')
            ->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$post->category_id])
            ->latest('published_at')
            ->limit(4)
            ->get();

        $comments = Comment::query()
            ->withTrashed()
            ->whereBelongsTo($post)
            ->whereNull('parent_id')
            ->where(function (Builder $query): void {
                $query->where('status', CommentStatus::Visible)
                    ->orWhereNotNull('deleted_at')
                    ->orWhereHas('replies', fn (Builder $query): Builder => $query
                        ->where('status', CommentStatus::Visible));
            })
            ->with([
                'user:id,name',
                'replies' => fn ($query) => $query
                    ->withTrashed()
                    ->where(function (Builder $query): void {
                        $query->where('status', CommentStatus::Visible)
                            ->orWhereNotNull('deleted_at');
                    })
                    ->with(['user:id,name', 'replyTo.user:id,name'])
                    ->oldest('created_at')
                    ->oldest('id'),
            ])
            ->latest('created_at')
            ->latest('id')
            ->paginate(10, ['*'], 'comments');

        return view('news.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'comments' => $comments,
            'reportReasons' => CommentReportReason::cases(),
            'isFavorited' => $request->user()?->hasVerifiedEmail()
                && Favorite::query()->whereBelongsTo($request->user())->whereBelongsTo($post)->exists(),
            'thumbnailUrl' => $post->thumbnail === null ? null : Storage::disk('public')->url($post->thumbnail),
            'openGraphImageUrl' => $post->thumbnail === null ? null : url(Storage::disk('public')->url($post->thumbnail)),
            'metaDescription' => $post->meta_description ?: Str::limit((string) $post->summary, 160, ''),
            'safeContent' => $contentSanitizer->sanitize($post->content),
        ]);
    }
}
