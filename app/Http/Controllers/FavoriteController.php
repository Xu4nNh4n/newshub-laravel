<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        return view('favorites.index', [
            'favorites' => Favorite::query()
                ->whereBelongsTo($request->user())
                ->whereHas('post', fn (Builder $query): Builder => $query->publiclyVisible())
                ->with(['post' => fn (BelongsTo $query): BelongsTo => $query
                    ->with(['author:id,name', 'category:id,name,slug'])])
                ->latest('created_at')
                ->latest('id')
                ->paginate(12),
        ]);
    }

    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless(Post::query()->publiclyVisible()->whereKey($post)->exists(), 404);

        $request->user()->favorites()->firstOrCreate(['post_id' => $post->id]);

        return back()->with('status', 'Đã lưu bài viết.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $request->user()->favorites()->where('post_id', $post->id)->delete();

        return back()->with('status', 'Đã bỏ lưu bài viết.');
    }
}
