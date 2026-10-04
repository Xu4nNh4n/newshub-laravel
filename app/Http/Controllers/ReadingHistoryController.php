<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadingHistoryController extends Controller
{
    /**
     * Display a paginated list of read posts for the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $timeframe = $request->query('timeframe', 'all');

        $latestViewIdsQuery = PostView::query()
            ->selectRaw('MAX(id) as id')
            ->where('user_id', $user->id)
            ->when($timeframe === 'today', fn (Builder $q): Builder => $q->where('viewed_at', '>=', now()->startOfDay()))
            ->when($timeframe === 'week', fn (Builder $q): Builder => $q->where('viewed_at', '>=', now()->subDays(7)->startOfDay()))
            ->when($timeframe === 'month', fn (Builder $q): Builder => $q->where('viewed_at', '>=', now()->subDays(30)->startOfDay()))
            ->groupBy('post_id');

        $readingHistory = PostView::query()
            ->whereIn('id', $latestViewIdsQuery)
            ->whereHas('post', fn (Builder $query): Builder => $query->publiclyVisible())
            ->with([
                'post' => fn (BelongsTo $query): BelongsTo => $query
                    ->with(['author:id,name', 'category:id,name,slug']),
            ])
            ->orderByDesc('viewed_at')
            ->paginate(12)
            ->withQueryString();

        $totalCount = PostView::query()
            ->where('user_id', $user->id)
            ->whereHas('post', fn (Builder $query): Builder => $query->publiclyVisible())
            ->distinct('post_id')
            ->count('post_id');

        return view('reading-history.index', [
            'readingHistory' => $readingHistory,
            'timeframe' => $timeframe,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Remove a single post from the user's reading history.
     */
    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $request->user()->postViews()->where('post_id', $post->id)->delete();

        return back()->with('status', 'Đã xóa bài viết khỏi lịch sử đọc.');
    }

    /**
     * Clear all reading history for the user.
     */
    public function clear(Request $request): RedirectResponse
    {
        $request->user()->postViews()->delete();

        return back()->with('status', 'Đã xóa toàn bộ lịch sử đọc bài viết.');
    }
}
