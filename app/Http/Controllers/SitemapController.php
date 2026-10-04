<?php

namespace App\Http\Controllers;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /** Return only URLs that are currently reachable from the public website. */
    public function __invoke(): Response
    {
        $posts = Post::query()
            ->publiclyVisible()
            ->select('id', 'slug', 'updated_at')
            ->orderBy('id')
            ->get();

        $categories = Category::query()
            ->where('status', CategoryStatus::Active)
            ->whereHas('posts', fn (Builder $query): Builder => $query->publiclyVisible())
            ->select('id', 'slug', 'updated_at')
            ->orderBy('id')
            ->get();

        $tags = Tag::query()
            ->whereHas('posts', fn (Builder $query): Builder => $query->publiclyVisible())
            ->select('id', 'slug', 'updated_at')
            ->orderBy('id')
            ->get();

        return response()
            ->view('sitemap', compact('posts', 'categories', 'tags'))
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=300');
    }
}
