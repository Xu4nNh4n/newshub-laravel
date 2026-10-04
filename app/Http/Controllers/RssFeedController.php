<?php

namespace App\Http\Controllers;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    public function index(): Response
    {
        return $this->feed();
    }

    public function category(Category $category): Response
    {
        abort_unless($category->status === CategoryStatus::Active, 404);

        return $this->feed($category);
    }

    private function feed(?Category $category = null): Response
    {
        $posts = Post::query()
            ->publiclyVisible()
            ->when(
                $category !== null,
                fn (Builder $query): Builder => $query->whereIn('category_id', $category->selfAndChildIds()),
            )
            ->with(['author:id,name', 'category:id,name,slug'])
            ->latest('published_at')
            ->latest('id')
            ->limit(50)
            ->get();

        return response()
            ->view('feed', [
                'posts' => $posts,
                'category' => $category,
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=300');
    }
}
