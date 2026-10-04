<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePostWorkflowRequest;
use App\Models\PostRequest;
use App\Services\PostRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostRequestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', PostRequest::class);
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(PostRequestStatus::class)],
        ]);
        $selectedStatus = PostRequestStatus::tryFrom($filters['status'] ?? '')
            ?? PostRequestStatus::Pending;

        return view('admin.post_requests.index', [
            'postRequests' => PostRequest::query()
                ->where('status', $selectedStatus)
                ->with([
                    'post:id,author_id,title,slug,status',
                    'author:id,name,email',
                    'handler:id,name',
                ])
                ->orderByDesc('priority')
                ->latest('created_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'selectedStatus' => $selectedStatus,
            'statuses' => PostRequestStatus::cases(),
            'breadcrumbs' => [
                ['label' => 'Yêu cầu bài viết', 'url' => null],
            ],
        ]);
    }

    public function update(
        UpdatePostWorkflowRequest $request,
        PostRequest $postRequest,
        PostRequestService $postRequests,
    ): RedirectResponse {
        $postRequests->handle(
            $postRequest,
            $request->user(),
            PostRequestStatus::from($request->string('status')->toString()),
            $request->filled('admin_notes') ? $request->string('admin_notes')->toString() : null,
        );

        return back()->with('status', 'Đã xử lý yêu cầu bài viết.');
    }
}
