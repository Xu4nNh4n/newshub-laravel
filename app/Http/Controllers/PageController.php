<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the Privacy Policy page.
     */
    public function privacy(): View
    {
        return view('pages.privacy');
    }

    /**
     * Display the Terms of Service page.
     */
    public function terms(): View
    {
        return view('pages.terms');
    }

    /**
     * Display the Content Moderation Guidelines page.
     */
    public function moderationPolicy(): View
    {
        return view('pages.moderation-policy');
    }

    /**
     * Display the Advertising and Editorial Contact page.
     */
    public function contact(): View
    {
        return view('pages.contact');
    }

    /**
     * Handle the contact / editorial feedback form submission.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'topic' => ['required', 'string', 'in:hotline,advertising,correction,copyright,other'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'email.required' => 'Vui lòng cung cấp địa chỉ email liên hệ.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'topic.required' => 'Vui lòng chọn chủ đề liên hệ.',
            'topic.in' => 'Chủ đề liên hệ không hợp lệ.',
            'subject.required' => 'Vui lòng nhập tiêu đề thư liên hệ.',
            'message.required' => 'Vui lòng nhập nội dung chi tiết cần liên hệ.',
            'message.min' => 'Nội dung thư phải có ít nhất 10 ký tự.',
        ]);

        Log::info('Contact message received via NewsHub portal', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'topic' => $validated['topic'],
            'subject' => $validated['subject'],
            'ip' => $request->ip(),
        ]);

        return back()->with('status', 'Cảm ơn bạn đã liên hệ! Ban Biên tập / Phòng Truyền thông & Quảng cáo NewsHub đã ghi nhận và sẽ phản hồi trong thời gian sớm nhất.');
    }
}
