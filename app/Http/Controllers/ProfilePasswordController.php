<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ProfilePasswordController extends Controller
{
    public function __invoke(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'password' => $request->string('password')->toString(),
        ]);
        $request->session()->regenerate();

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'action' => 'password.updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'description' => 'Đổi mật khẩu tài khoản thành công.',
        ]);

        return back()->with('status', 'Đã đổi mật khẩu.');
    }
}
