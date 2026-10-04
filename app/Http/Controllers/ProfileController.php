<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\UserAvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->loadCount(['comments', 'favorites', 'posts', 'postViews']);

        return view('profile.edit', [
            'avatarUrl' => $user->avatar === null
                ? null
                : Storage::disk('public')->url($user->avatar),
            'stats' => [
                'comments_count' => $user->comments_count,
                'favorites_count' => $user->favorites_count,
                'posts_count' => $user->posts_count,
            ],
            'postViewsCount' => $user->post_views_count,
        ]);
    }

    public function update(UpdateProfileRequest $request, UserAvatarService $avatars): RedirectResponse
    {
        $user = $request->user();

        $avatars->update(
            $user,
            $request->string('name')->toString(),
            $request->file('avatar'),
            $request->has('email') ? $request->string('email')->toString() : null,
            $request->boolean('remove_avatar'),
        );

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'action' => 'profile.updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Cập nhật thông tin hồ sơ cá nhân.',
        ]);

        return redirect()->route('profile.edit')->with('status', 'Đã cập nhật hồ sơ.');
    }
}
