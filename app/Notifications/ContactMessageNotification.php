<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageNotification extends Notification
{
    /**
     * @param  array{name:string, email:string, phone?:string|null, topic:string, subject:string, message:string}  $contact
     */
    public function __construct(public readonly array $contact) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /** Build a plain-text message so visitor input is never interpreted as markup. */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Liên hệ BaoUT] '.$this->contact['subject'])
            ->replyTo($this->contact['email'], $this->contact['name'])
            ->text('mail.contact-message-text', ['contact' => $this->contact]);
    }
}
