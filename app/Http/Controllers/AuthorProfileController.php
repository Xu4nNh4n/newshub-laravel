<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AuthorProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user): View
    {
        $posts = Post::query()
            ->publiclyVisible()
            ->whereBelongsTo($user, 'author');

        abort_unless($posts->exists(), 404);

        return view('authors.show', [
            'author' => $user,
            'avatarUrl' => $user->avatar === null ? null : Storage::disk('public')->url($user->avatar),
            'posts' => $posts
                ->with(['author:id,name', 'category:id,name,slug'])
                ->latest('published_at')
                ->latest('id')
                ->paginate(12),
        ]);
    }
}
