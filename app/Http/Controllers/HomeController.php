<?php

namespace App\Http\Controllers;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $baseQuery = fn () => Post::query()->publiclyVisible()->with(['author:id,name', 'category:id,name,slug']);

        return view('home', [
            'featuredPosts' => $baseQuery()->where('is_featured', true)->latest('published_at')->limit(3)->get(),
            'latestPosts' => $baseQuery()->latest('published_at')->latest('id')->limit(9)->get(),
            'popularPosts' => $baseQuery()->orderByDesc('view_count')->latest('published_at')->limit(5)->get(),
            'categories' => Category::query()->where('status', CategoryStatus::Active)->orderBy('name')->get(),
            'categorySections' => Category::query()
                ->where('status', CategoryStatus::Active)
                ->whereHas('posts', fn (Builder $query): Builder => $query->publiclyVisible())
                ->with(['posts' => function (HasMany $query): void {
                    $query->publiclyVisible()
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
