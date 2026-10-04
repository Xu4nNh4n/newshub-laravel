<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Category::class);

        $allCategories = Category::query()
            ->withCount('posts')
            ->with(['children' => fn (HasMany $query): HasMany => $query->withCount('posts')->orderBy('name')])
            ->orderBy('name')
            ->get();
        $parentCategories = $allCategories->whereNull('parent_id')->values();
        $parentIds = $parentCategories->modelKeys();

        return view('admin.categories.index', [
            'allCategories' => $allCategories,
            'parentCategories' => $parentCategories,
            'orphanCategories' => $allCategories
                ->filter(fn (Category $category): bool => $category->parent_id !== null
                    && ! in_array($category->parent_id, $parentIds, true))
                ->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Category::class);

        return view('admin.categories.form', [
            'parentCategories' => Category::query()->parents()->orderBy('name')->get(['id', 'name']),
            'breadcrumbs' => [
                ['label' => 'Danh mục', 'url' => route('admin.categories.index')],
                ['label' => 'Thêm chuyên mục', 'url' => null],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse|JsonResponse
    {
        $category = Category::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã tạo chuyên mục.',
                'data' => $category->only(['id', 'parent_id', 'name', 'slug', 'description', 'status']),
            ], 201);
        }

        return redirect()->route('admin.categories.index')->with('status', 'Đã tạo chuyên mục.');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Category $category): View
    {
        Gate::authorize('update', $category);

        return view('admin.categories.form', [
            'category' => $category,
            'parentCategories' => Category::query()
                ->parents()
                ->whereKeyNot($category->id)
                ->orderBy('name')
                ->get(['id', 'name']),
            'breadcrumbs' => [
                ['label' => 'Danh mục', 'url' => route('admin.categories.index')],
                ['label' => 'Sửa chuyên mục', 'url' => null],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        $category->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật chuyên mục.',
                'data' => $category->only(['id', 'parent_id', 'name', 'slug', 'description', 'status']),
            ]);
        }

        return redirect()->route('admin.categories.index')->with('status', 'Đã cập nhật chuyên mục.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $category);

        if ($category->posts()->exists() || $category->children()->exists()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa chuyên mục đang có bài viết hoặc danh mục con.',
                ], 422);
            }

            throw ValidationException::withMessages([
                'category' => 'Không thể xóa chuyên mục đang có bài viết hoặc danh mục con.',
            ]);
        }

        $category->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa chuyên mục.',
            ]);
        }

        return redirect()->route('admin.categories.index')->with('status', 'Đã xóa chuyên mục.');
    }
}
