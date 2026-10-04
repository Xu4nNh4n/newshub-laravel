<?php

namespace App\Notifications;

use App\Enums\PostRequestStatus;
use App\Enums\PostRequestType;
use App\Models\PostRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostRequestHandledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PostRequest $postRequest,
        public PostRequestStatus $status,
        public ?string $adminNotes = null,
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
        $typeLabel = $this->postRequest->type === PostRequestType::Removal ? 'gỡ bài' : 'đính chính';
        $isApproved = $this->status === PostRequestStatus::Approved;

        return [
            'type' => 'post_request',
            'title' => 'Yêu cầu '.$typeLabel.' đã được xử lý',
            'message' => $isApproved
                ? 'Ban biên tập đã chấp thuận yêu cầu '.$typeLabel.' đối với bài viết "'.$this->postRequest->post->title.'".'
                : 'Ban biên tập đã từ chối yêu cầu '.$typeLabel.': '.($this->adminNotes ?: 'Không đủ căn cứ xử lý.'),
            'status' => $this->status->value,
            'post_id' => $this->postRequest->post_id,
            'url' => route('author.posts.index'),
        ];
    }
}
