<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuthorApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectAuthorApplicationRequest;
use App\Models\AuthorApplication;
use App\Services\AuthorApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthorApplicationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', AuthorApplication::class);
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(AuthorApplicationStatus::class)],
        ]);
        $status = AuthorApplicationStatus::tryFrom($filters['status'] ?? '')
            ?? AuthorApplicationStatus::Pending;

        return view('admin.author-applications.index', [
            'applications' => AuthorApplication::query()
                ->where('status', $status)
                ->with(['user:id,name,email,role', 'category:id,name', 'reviewer:id,name'])
                ->latest('created_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'selectedStatus' => $status,
            'statuses' => AuthorApplicationStatus::cases(),
            'breadcrumbs' => [
                ['label' => 'Đơn ứng tuyển tác giả', 'url' => null],
            ],
        ]);
    }

    public function approve(
        Request $request,
        AuthorApplication $application,
        AuthorApplicationService $applications,
    ): RedirectResponse {
        Gate::authorize('approve', $application);
        $applications->approve($application, $request->user());

        return back()->with('status', 'Đã phê duyệt đơn và cấp quyền tác giả.');
    }

    public function reject(
        RejectAuthorApplicationRequest $request,
        AuthorApplication $application,
        AuthorApplicationService $applications,
    ): RedirectResponse {
        $applications->reject($application, $request->user(), $request->string('reason')->toString());

        return back()->with('status', 'Đã từ chối đơn ứng tuyển tác giả.');
    }
}
