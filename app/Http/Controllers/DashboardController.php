<?php

namespace App\Http\Controllers;

use App\Enums\CategoryStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, DashboardStatsService $stats): View
    {
        $user = $request->user();
        $canWrite = in_array($user->role, [UserRole::Author, UserRole::Admin], true);

        if ($user->role === UserRole::User) {
            $user->load('latestAuthorApplication.category:id,name');
        }

        return view('dashboard.index', [
            'adminStats' => $user->role === UserRole::Admin ? $stats->forAdmin() : null,
            'authorStats' => $user->hasVerifiedEmail() && $canWrite
                ? $stats->forAuthor($user)
                : null,
            'authorApplication' => $user->role === UserRole::User
                ? $user->latestAuthorApplication
                : null,
            'authorApplicationCategories' => $user->role === UserRole::User
                ? Category::query()
                    ->where('status', CategoryStatus::Active)
                    ->orderBy('name')
                    ->get(['id', 'name'])
                : collect(),
        ]);
    }
}
