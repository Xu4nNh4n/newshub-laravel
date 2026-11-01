<?php

namespace App\Http\Controllers\Author;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StorePostRequest;
use App\Http\Requests\Author\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostContentSanitizer;
use App\Services\PostThumbnailService;
use App\Services\PostWorkflowService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(PostStatus::class)],
        ]);

        return view('author.posts.index', [
            'posts' => Post::query()
                ->whereBelongsTo($request->user(), 'author')
                ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
                ->with(['category', 'requests'])
                ->latest('id')
                ->paginate(15)
                ->withQueryString(),
            'filters' => $filters,
            'statuses' => PostStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, PostContentSanitizer $contentSanitizer): View
    {
        Gate::authorize('create', Post::class);

        return view('author.posts.form', [
            ...$this->formData(),
            'editorContent' => $contentSanitizer->sanitize((string) $request->old('content', '')),
            'breadcrumbs' => $this->formBreadcrumbs('Viết bài'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StorePostRequest $request,
        PostThumbnailService $thumbnails,
        PostWorkflowService $workflow,
        PostContentSanitizer $contentSanitizer,
    ): RedirectResponse {
        $shouldPublish = $request->string('action')->toString() === 'publish'
            && $request->user()->role === UserRole::Admin;
        $publishedAt = $shouldPublish && $request->filled('published_at')
            ? CarbonImmutable::parse($request->string('published_at')->toString())
            : null;
        $thumbnailPath = $request->hasFile('thumbnail') ? $thumbnails->store($request->file('thumbnail')) : null;

        try {
            $post = DB::transaction(function () use ($request, $thumbnailPath, $shouldPublish, $publishedAt, $workflow, $contentSanitizer): Post {
                $attributes = $request->safe()->except(['thumbnail', 'action', 'published_at']);
                $attributes['content'] = $contentSanitizer->sanitize($attributes['content']);
                $attributes['thumbnail'] = $thumbnailPath;
                $post = $request->user()->posts()->create($attributes);
                if ($shouldPublish) {
                    $workflow->publish($post, $request->user(), $publishedAt);
                }

                return $post;
            });
        } catch (\Throwable $exception) {
            $thumbnails->delete($thumbnailPath);
            throw $exception;
        }

        return $shouldPublish
            ? redirect()->route('author.posts.index')->with('status', 'Đã xuất bản bài viết thành công.')
            : redirect()->route('author.posts.edit', $post)->with('status', 'Đã lưu bản nháp.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Request $request, Post $post, PostContentSanitizer $contentSanitizer): View
    {
        Gate::authorize('update', $post);

        return view('author.posts.form', [
            ...$this->formData(),
            'post' => $post->load('requests'),
            'editorContent' => $contentSanitizer->sanitize((string) $request->old('content', $post->content)),
            'breadcrumbs' => $this->formBreadcrumbs('Sửa bài viết'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdatePostRequest $request,
        Post $post,
        PostThumbnailService $thumbnails,
        PostWorkflowService $workflow,
        PostContentSanitizer $contentSanitizer,
    ): RedirectResponse {
        $action = $request->string('action')->toString();
        $shouldPublish = $action === 'publish' && $request->user()->role === UserRole::Admin;

        if ($shouldPublish) {
            Gate::authorize('publish', $post);
        }

        $publishedAt = $shouldPublish && $request->filled('published_at')
            ? CarbonImmutable::parse($request->string('published_at')->toString())
            : null;
        $oldThumbnail = $post->thumbnail;
        $newThumbnail = $request->hasFile('thumbnail') ? $thumbnails->store($request->file('thumbnail')) : null;

        try {
            DB::transaction(function () use ($request, $post, $newThumbnail, $action, $shouldPublish, $publishedAt, $workflow, $contentSanitizer): void {
                $attributes = $request->safe()->except([
                    'thumbnail',
                    'remove_thumbnail',
                    'action',
                    'published_at',
                ]);
                $attributes['content'] = $contentSanitizer->sanitize($attributes['content']);
                if ($newThumbnail !== null || $request->boolean('remove_thumbnail')) {
                    $attributes['thumbnail'] = $newThumbnail;
                }

                if ($action === 'draft') {
                    $attributes['status'] = PostStatus::Draft;
                    $attributes['published_at'] = null;
                    $attributes['rejection_reason'] = null;
                }

                $post->update($attributes);
                if ($shouldPublish) {
                    $workflow->publish($post, $request->user(), $publishedAt);
                }

            });
        } catch (\Throwable $exception) {
            $thumbnails->delete($newThumbnail);
            throw $exception;
        }

        if ($oldThumbnail !== null && ($newThumbnail !== null || $request->boolean('remove_thumbnail'))) {
            $thumbnails->delete($oldThumbnail);
        }

        return $shouldPublish
            ? redirect()->route('author.posts.index')->with('status', 'Đã xuất bản bài viết thành công.')
            : back()->with('status', 'Đã cập nhật bài viết.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, PostThumbnailService $thumbnails): RedirectResponse
    {
        Gate::authorize('delete', $post);
        $post->delete();
        $thumbnails->delete($post->thumbnail);

        return redirect()->route('author.posts.index')->with('status', 'Đã xóa bài viết.');
    }

    /** @return array{categories: Collection} */
    private function formData(): array
    {
        return [
            'categories' => Category::query()->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    /** @return list<array{label:string, url:?string}> */
    private function formBreadcrumbs(string $currentLabel): array
    {
        return [
            ['label' => 'Bài viết của tôi', 'url' => route('author.posts.index')],
            ['label' => $currentLabel, 'url' => null],
        ];
    }
}
