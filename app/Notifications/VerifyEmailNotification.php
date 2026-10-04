<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::integer('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );

        return (new MailMessage)
            ->subject('Xác minh địa chỉ email NewsHub')
            ->view(['html' => 'mail.auth-action', 'text' => 'mail.auth-action-text'], [
                'preheader' => 'Xác minh email để hoàn tất tài khoản NewsHub.',
                'eyebrow' => 'BẢO MẬT TÀI KHOẢN',
                'title' => 'Chỉ còn một bước nữa',
                'recipientName' => $notifiable->name,
                'intro' => 'Cảm ơn bạn đã tham gia NewsHub. Hãy xác minh địa chỉ email để bình luận, lưu bài viết và sử dụng đầy đủ các tính năng.',
                'actionLabel' => 'Xác minh email',
                'actionUrl' => $verificationUrl,
                'expiryText' => 'Liên kết có hiệu lực trong '.Config::integer('auth.verification.expire', 60).' phút.',
                'securityText' => 'Nếu bạn không tạo tài khoản NewsHub, hãy bỏ qua email này. Không cần thực hiện thêm thao tác nào.',
                'accentColor' => '#22d3ee',
                'accentTextColor' => '#083344',
                'accentSoftColor' => '#cffafe',
            ]);
    }
}
