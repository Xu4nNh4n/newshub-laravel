<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentRepliedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Post $post,
        public User $replier,
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
        return [
            'type' => 'comment_reply',
            'title' => 'Phản hồi mới về bình luận của bạn',
            'message' => $this->replier->name.' đã trả lời bình luận của bạn tại bài viết "'.$this->post->title.'".',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'comment_id' => $this->comment->id,
            'replier_name' => $this->replier->name,
            'url' => route('news.show', $this->post->slug).'#comment-'.$this->comment->id,
        ];
    }
}
