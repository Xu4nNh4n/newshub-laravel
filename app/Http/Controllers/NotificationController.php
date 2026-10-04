<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of user notifications or return JSON for header dropdown.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        if ($request->wantsJson()) {
            $notifications = $user->notifications()->take(10)->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'read' => $notification->read(),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data,
                ];
            });

            return response()->json([
                'unread_count' => $user->unreadNotifications()->count(),
                'notifications' => $notifications,
            ]);
        }

        $notifications = $user->notifications()->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        $redirectUrl = $notification?->data['url'] ?? null;

        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return back()->with('status', 'Đã đánh dấu thông báo là đã đọc.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        return back()->with('status', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
