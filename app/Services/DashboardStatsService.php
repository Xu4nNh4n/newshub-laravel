<?php

namespace App\Services;

use App\Enums\AuthorApplicationStatus;
use App\Enums\CommentReportStatus;
use App\Enums\PostRequestStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\AuthorApplication;
use App\Models\Category;
use App\Models\CommentReport;
use App\Models\Post;
use App\Models\PostRequest;
use App\Models\PostView;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class DashboardStatsService
{
    /** Return system totals and bounded trend data for the admin dashboard. */
    public function forAdmin(): array
    {
        $pendingModerationCounts = $this->pendingModerationCounts();

        return [
            'users_total' => User::query()->where('role', UserRole::User)->count(),
            'authors_total' => User::query()->where('role', UserRole::Author)->count(),
            'blocked_total' => User::query()->where('status', UserStatus::Blocked)->count(),
            'posts_total' => Post::query()->count(),
            'pending_posts' => $pendingModerationCounts['pending_posts_count'],
            'published_total' => Post::query()->where('status', PostStatus::Published)->count(),
            'published_visible' => Post::query()->publiclyVisible()->count(),
            'pending_reports' => $pendingModerationCounts['pending_reports_count'],
            'pending_applications' => $pendingModerationCounts['pending_applications_count'],
            'pending_post_requests' => $pendingModerationCounts['pending_post_requests_count'],
            'top_posts' => Post::query()
                ->publiclyVisible()
                ->select(['id', 'author_id', 'category_id', 'title', 'slug', 'view_count'])
                ->with(['author:id,name', 'category:id,name'])
                ->orderByDesc('view_count')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
            'posts_by_month' => $this->postsByMonth(),
            'views_by_day' => $this->viewsByDay(),
            'top_categories' => Category::query()
                ->select(['id', 'name'])
                ->withCount(['posts as published_posts_count' => fn ($query) => $query->publiclyVisible()])
                ->withSum(['posts as published_views_sum' => fn ($query) => $query->publiclyVisible()], 'view_count')
                ->orderByDesc('published_views_sum')
                ->orderByDesc('published_posts_count')
                ->limit(5)
                ->get(),
        ];
    }

    /** @return array{pending_posts_count:int, pending_reports_count:int, pending_applications_count:int, pending_post_requests_count:int} */
    public function pendingModerationCounts(): array
    {
        return [
            'pending_posts_count' => Post::query()->where('status', PostStatus::PendingReview)->count(),
            'pending_reports_count' => CommentReport::query()->where('status', CommentReportStatus::Pending)->count(),
            'pending_applications_count' => AuthorApplication::query()
                ->where('status', AuthorApplicationStatus::Pending)
                ->count(),
            'pending_post_requests_count' => PostRequest::query()
                ->where('status', PostRequestStatus::Pending)
                ->count(),
        ];
    }

    /** @return array{posts_total:int, drafts:int, pending_posts:int, rejected_posts:int, published_posts:int, total_views:int, top_posts:Collection<int, Post>} */
    public function forAuthor(User $author): array
    {
        $posts = Post::query()->where('author_id', $author->id);

        return [
            'posts_total' => (clone $posts)->count(),
            'drafts' => (clone $posts)->where('status', PostStatus::Draft)->count(),
            'pending_posts' => (clone $posts)->where('status', PostStatus::PendingReview)->count(),
            'rejected_posts' => (clone $posts)->where('status', PostStatus::Rejected)->count(),
            'published_posts' => (clone $posts)->where('status', PostStatus::Published)->count(),
            'total_views' => (int) (clone $posts)->sum('view_count'),
            'top_posts' => (clone $posts)
                ->select(['id', 'category_id', 'title', 'slug', 'status', 'view_count'])
                ->with('category:id,name')
                ->orderByDesc('view_count')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ];
    }

    /** @return list<array{label:string, count:int}> */
    private function postsByMonth(): array
    {
        $firstMonth = CarbonImmutable::now()->startOfMonth()->subMonths(5);

        return collect(range(0, 5))->map(function (int $offset) use ($firstMonth): array {
            $month = $firstMonth->addMonths($offset);

            return [
                'label' => $month->format('m/Y'),
                'count' => Post::query()->whereBetween('created_at', [$month, $month->endOfMonth()])->count(),
            ];
        })->all();
    }

    /** @return list<array{label:string, count:int}> */
    private function viewsByDay(): array
    {
        $firstDay = CarbonImmutable::now()->startOfDay()->subDays(6);

        return collect(range(0, 6))->map(function (int $offset) use ($firstDay): array {
            $day = $firstDay->addDays($offset);

            return [
                'label' => $day->format('d/m'),
                'count' => PostView::query()->whereBetween('viewed_at', [$day, $day->endOfDay()])->count(),
            ];
        })->all();
    }
}
