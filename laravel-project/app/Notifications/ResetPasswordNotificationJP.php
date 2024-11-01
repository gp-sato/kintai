<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotificationJP extends Notification
{
    use Queueable;

    public $token;

    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->url = route('password.reset', [$token]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('パスワードリセットURLの送付')
            ->greeting('いつもご利用頂きありがとうございます')
            ->line('こちらからパスワードリセットを行ってください')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('パスワードリセット', $this->url)
            ->line('このリンクは60分後に無効になります')
            ->line('このメールに心当たりがなければ無視してください')
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
