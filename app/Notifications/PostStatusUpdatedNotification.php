<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Post $post,
        public string $action, // 'approved' or 'rejected'
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
        $isApproved = $this->action === 'approved';

        return [
            'type' => 'post_status',
            'title' => $isApproved ? 'Bài viết đã được duyệt xuất bản' : 'Bài viết bị từ chối duyệt',
            'message' => $isApproved
                ? 'Bài viết "'.$this->post->title.'" đã được Ban biên tập phê duyệt xuất bản.'
                : 'Bài viết "'.$this->post->title.'" bị từ chối: '.($this->reason ?: 'Chưa đạt yêu cầu tòa soạn.'),
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'action' => $this->action,
            'reason' => $this->reason,
            'url' => $isApproved
                ? route('news.show', $this->post->slug)
                : route('author.posts.edit', $this->post),
        ];
    }
}
