<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use SensitiveParameter;

class ResetPasswordNotification extends Notification
{
    public function __construct(#[SensitiveParameter] public readonly string $token) {}

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
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
        $expiresInMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Đặt lại mật khẩu NewsHub')
            ->view(['html' => 'mail.auth-action', 'text' => 'mail.auth-action-text'], [
                'preheader' => 'Yêu cầu đặt lại mật khẩu tài khoản NewsHub.',
                'eyebrow' => 'YÊU CẦU BẢO MẬT',
                'title' => 'Đặt lại mật khẩu',
                'recipientName' => $notifiable->name,
                'intro' => 'NewsHub vừa nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Sử dụng nút bên dưới để tạo mật khẩu mới.',
                'actionLabel' => 'Đặt lại mật khẩu',
                'actionUrl' => $resetUrl,
                'expiryText' => "Liên kết có hiệu lực trong {$expiresInMinutes} phút.",
                'securityText' => 'Nếu bạn không gửi yêu cầu này, hãy bỏ qua email. Mật khẩu hiện tại của bạn sẽ không thay đổi và tuyệt đối không chia sẻ liên kết này.',
                'accentColor' => '#fbbf24',
                'accentTextColor' => '#451a03',
                'accentSoftColor' => '#fef3c7',
            ]);
    }
}
