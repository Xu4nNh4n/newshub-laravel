<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserActivityHistoryController extends Controller
{
    /**
     * Display a timeline of the user's activity and change history.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $type = $request->query('type', 'all');
        $timeframe = $request->query('timeframe', 'all');

        $authorPostIds = in_array($user->role, [UserRole::Author, UserRole::Admin], true)
            ? $user->posts()->pluck('id')->all()
            : [];

        $baseQuery = fn (): Builder => ActivityLog::query()
            ->where(function (Builder $builder) use ($user, $authorPostIds): void {
                $builder->where('user_id', $user->id);

                if (! empty($authorPostIds)) {
                    $builder->orWhere(function (Builder $postQ) use ($authorPostIds): void {
                        $postQ->where('subject_type', Post::class)
                            ->whereIn('subject_id', $authorPostIds);
                    });
                }

                $builder->orWhere(function (Builder $userQ) use ($user): void {
                    $userQ->where('subject_type', User::class)
                        ->where('subject_id', $user->id);
                });
            });

        $logs = $baseQuery()
            ->when($type === 'account', fn (Builder $q): Builder => $q->whereIn('action', [
                'profile.updated',
                'password.updated',
                'user.status_updated',
                'user.role_updated',
            ]))
            ->when($type === 'posts', fn (Builder $q): Builder => $q->where(function (Builder $sub): void {
                $sub->where('action', 'like', 'post.%')
                    ->orWhere('action', 'like', 'post_request.%');
            }))
            ->when($type === 'interactions', fn (Builder $q): Builder => $q->where(function (Builder $sub): void {
                $sub->where('action', 'like', 'comment%')
                    ->orWhere('action', 'like', 'application%')
                    ->orWhere('action', 'like', 'favorite%');
            }))
            ->when($timeframe === 'today', fn (Builder $q): Builder => $q->where('created_at', '>=', now()->startOfDay()))
            ->when($timeframe === 'week', fn (Builder $q): Builder => $q->where('created_at', '>=', now()->subDays(7)->startOfDay()))
            ->when($timeframe === 'month', fn (Builder $q): Builder => $q->where('created_at', '>=', now()->subDays(30)->startOfDay()))
            ->with('user:id,name,email,avatar')
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => $baseQuery()->count(),
            'posts_count' => $baseQuery()->where(function (Builder $sub): void {
                $sub->where('action', 'like', 'post.%')
                    ->orWhere('action', 'like', 'post_request.%');
            })->count(),
            'account_count' => $baseQuery()->whereIn('action', [
                'profile.updated',
                'password.updated',
                'user.status_updated',
                'user.role_updated',
            ])->count(),
            'interactions_count' => $baseQuery()->where(function (Builder $sub): void {
                $sub->where('action', 'like', 'comment%')
                    ->orWhere('action', 'like', 'application%')
                    ->orWhere('action', 'like', 'favorite%');
            })->count(),
        ];

        return view('profile.activity', [
            'logs' => $logs,
            'type' => $type,
            'timeframe' => $timeframe,
            'stats' => $stats,
            'isAuthor' => in_array($user->role, [UserRole::Author, UserRole::Admin], true),
        ]);
    }
}
