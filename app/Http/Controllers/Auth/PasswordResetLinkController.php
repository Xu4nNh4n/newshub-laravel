<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** Send a reset link while returning the same response for every email. */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->safe()->only('email'));

        return back()->with('status', 'Nếu email tồn tại, hệ thống đã gửi liên kết đặt lại mật khẩu.');
    }
}
