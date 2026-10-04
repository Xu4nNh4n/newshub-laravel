<?php

namespace App\Http\ViewComposers;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardSidebarComposer
{
    public function __construct(private readonly DashboardStatsService $dashboardStats) {}

    public function compose(View $view): void
    {
        $user = Auth::user();

        if (! $user instanceof User || $user->role !== UserRole::Admin) {
            return;
        }

        $viewData = $view->getData();
        $adminStats = is_array($viewData['adminStats'] ?? null) ? $viewData['adminStats'] : [];

        if (
            array_key_exists('pending_posts', $adminStats)
            && array_key_exists('pending_reports', $adminStats)
            && array_key_exists('pending_applications', $adminStats)
            && array_key_exists('pending_post_requests', $adminStats)
        ) {
            $pendingPostsCount = (int) $adminStats['pending_posts'];
            $pendingReportsCount = (int) $adminStats['pending_reports'];
            $pendingApplicationsCount = (int) $adminStats['pending_applications'];
            $pendingPostRequestsCount = (int) $adminStats['pending_post_requests'];
        } else {
            $counts = $this->dashboardStats->pendingModerationCounts();
            $pendingPostsCount = $counts['pending_posts_count'];
            $pendingReportsCount = $counts['pending_reports_count'];
            $pendingApplicationsCount = $counts['pending_applications_count'];
            $pendingPostRequestsCount = $counts['pending_post_requests_count'];
        }

        $view->with([
            'pending_posts_count' => $pendingPostsCount,
            'pending_reports_count' => $pendingReportsCount,
            'pending_applications_count' => $pendingApplicationsCount,
            'pending_post_requests_count' => $pendingPostRequestsCount,
            'adminStats' => [
                ...$adminStats,
                'pending_posts' => $pendingPostsCount,
                'pending_reports' => $pendingReportsCount,
                'pending_applications' => $pendingApplicationsCount,
                'pending_post_requests' => $pendingPostRequestsCount,
            ],
        ]);
    }
}
