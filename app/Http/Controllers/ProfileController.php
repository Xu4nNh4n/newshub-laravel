<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->loadCount(['comments', 'posts']);

        return view('profile.edit', [
            'stats' => [
                'comments_count' => $user->comments_count,
                'posts_count' => $user->posts_count,
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $email = $request->string('email')->toString();
        $emailChanged = $email !== $user->email;

        $user->forceFill([
            'name' => $request->string('name')->toString(),
            'email' => $email,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ])->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()->route('profile.edit')->with('status', 'Đã cập nhật hồ sơ.');
    }
}
