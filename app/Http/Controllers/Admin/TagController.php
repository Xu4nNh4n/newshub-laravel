<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Tag::class);

        return view('admin.tags.index', [
            'tags' => Tag::query()->withCount('posts')->latest('id')->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Tag::class);

        return view('admin.tags.form', [
            'breadcrumbs' => [
                ['label' => 'Thẻ', 'url' => route('admin.tags.index')],
                ['label' => 'Thêm thẻ', 'url' => null],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): RedirectResponse|JsonResponse
    {
        $tag = Tag::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã tạo thẻ.',
                'data' => $tag->only(['id', 'name', 'slug']),
            ], 201);
        }

        return redirect()->route('admin.tags.index')->with('status', 'Đã tạo thẻ.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Tag $tag): View
    {
        Gate::authorize('update', $tag);

        return view('admin.tags.form', [
            'tag' => $tag,
            'breadcrumbs' => [
                ['label' => 'Thẻ', 'url' => route('admin.tags.index')],
                ['label' => 'Sửa thẻ', 'url' => null],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse|JsonResponse
    {
        $tag->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thẻ.',
                'data' => $tag->only(['id', 'name', 'slug']),
            ]);
        }

        return redirect()->route('admin.tags.index')->with('status', 'Đã cập nhật thẻ.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Tag $tag): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $tag);
        $tag->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thẻ.',
            ]);
        }

        return redirect()->route('admin.tags.index')->with('status', 'Đã xóa thẻ.');
    }
}
