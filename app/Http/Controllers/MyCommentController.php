<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCommentController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('profile.comments', [
            'comments' => Comment::query()
                ->withTrashed()
                ->whereBelongsTo($request->user())
                ->with(['post' => fn (BelongsTo $query): BelongsTo => $query
                    ->withTrashed()
                    ->with('category:id,status')
                    ->select('id', 'category_id', 'title', 'slug', 'status', 'published_at', 'deleted_at')])
                ->latest('created_at')
                ->latest('id')
                ->paginate(15),
        ]);
    }
}
