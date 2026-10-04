<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserAccessRequest;
use App\Models\User;
use App\Services\UserAccessService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', Rule::in([UserRole::User->value, UserRole::Author->value])],
            'status' => ['nullable', Rule::enum(UserStatus::class)],
        ]);

        return view('admin.users.index', [
            'users' => User::query()
                ->where('role', '!=', UserRole::Admin)
                ->when($filters['q'] ?? null, function (Builder $query, string $keyword): void {
                    $query->where(function (Builder $query) use ($keyword): void {
                        $query->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->when($filters['role'] ?? null, fn (Builder $query, string $role): Builder => $query->where('role', $role))
                ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
                ->withCount(['posts', 'comments'])
                ->latest('created_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function update(
        UpdateUserAccessRequest $request,
        User $user,
        UserAccessService $access,
    ): RedirectResponse {
        $changed = $access->update(
            $user,
            $request->user(),
            UserRole::from($request->string('role')->toString()),
            UserStatus::from($request->string('status')->toString()),
        );

        return back()->with('status', $changed ? 'Đã cập nhật quyền tài khoản.' : 'Tài khoản không có thay đổi.');
    }

    public function sendResetLink(
        Request $request,
        User $user,
        UserAccessService $access,
    ): RedirectResponse {
        Gate::authorize('update', $user);

        $access->sendResetLink($user, $request->user());

        return back()->with('status', "Đã gửi liên kết đặt lại mật khẩu đến {$user->email}.");
    }
}
