<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const LATEST_POSTS = 6;

    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $baseQuery = fn () => Post::query()->publiclyVisible()->with(['author:id,name', 'category:id,name,slug']);

        $featuredPosts = $baseQuery()->latest('published_at')->latest('id')->limit(3)->get();
        $latestPosts = $baseQuery()
            ->whereKeyNot($featuredPosts->modelKeys())
            ->latest('published_at')
            ->latest('id')
            ->limit(self::LATEST_POSTS + 1)
            ->get();
        $listedPostIds = [...$featuredPosts->modelKeys(), ...$latestPosts->take(self::LATEST_POSTS)->modelKeys()];

        return view('home', [
            'featuredPosts' => $featuredPosts,
            'latestPosts' => $latestPosts->take(self::LATEST_POSTS),
            'hasMorePosts' => $latestPosts->count() > self::LATEST_POSTS,
            'popularPosts' => $baseQuery()->orderByDesc('view_count')->latest('published_at')->limit(5)->get(),
            'categories' => Category::query()->publiclyVisible()->orderBy('name')->get(),
            'categorySections' => Category::query()
                ->publiclyVisible()
                ->whereHas('posts', fn (Builder $query): Builder => $query->publiclyVisible()->whereKeyNot($listedPostIds))
                ->with(['posts' => function (HasMany $query) use ($listedPostIds): void {
                    $query->publiclyVisible()
                        ->whereKeyNot($listedPostIds)
                        ->with(['author:id,name', 'category:id,name,slug'])
                        ->latest('published_at')
                        ->latest('id')
                        ->limit(3);
                }])
                ->orderBy('name')
                ->limit(4)
                ->get(),
        ]);
    }
}
