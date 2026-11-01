<?php

namespace App\Http\Controllers;

use App\Http\Requests\Author\StoreAuthorApplicationRequest;
use App\Services\AuthorApplicationService;
use Illuminate\Http\RedirectResponse;

class AuthorApplicationController extends Controller
{
    public function store(
        StoreAuthorApplicationRequest $request,
        AuthorApplicationService $applications,
    ): RedirectResponse {
        $applications->submit(
            $request->user(),
            $request->safe()->only(['category_id', 'bio']),
            $request->file('cv'),
        );

        return redirect()->route('dashboard')->with('status', 'Đã gửi đơn ứng tuyển tác giả.');
    }
}
