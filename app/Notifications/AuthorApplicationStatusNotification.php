<?php

namespace App\Notifications;

use App\Models\AuthorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AuthorApplicationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AuthorApplication $application,
        public string $status, // 'approved' or 'rejected'
        public ?string $reason = null,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->status === 'approved';

        return [
            'type' => 'author_application',
            'title' => $isApproved ? 'Đơn ứng tuyển tác giả đã được duyệt!' : 'Đơn ứng tuyển tác giả bị từ chối',
            'message' => $isApproved
                ? 'Chúc mừng! Bạn đã chính thức trở thành Tác giả trên NewsHub. Hãy bắt đầu sáng tạo bài viết mới.'
                : 'Đơn ứng tuyển tác giả của bạn chưa được duyệt: '.($this->reason ?: 'Vui lòng hoàn thiện hồ sơ và thử lại sau.'),
            'status' => $this->status,
            'reason' => $this->reason,
            'url' => $isApproved ? route('author.posts.create') : route('dashboard'),
        ];
    }
}
