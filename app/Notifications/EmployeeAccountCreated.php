<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $temporaryPassword)
    {
        //
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
            ->subject('تم إنشاء حسابك على المنصة')
            ->line('تم إنشاء حساب لك من قبل إدارة مؤسستك.')
            ->line('بريدك الإلكتروني: ' . $notifiable->email)
            ->line('كلمة السر المؤقتة: ' . $this->temporaryPassword)
            ->line('يرجى تسجيل الدخول وتغيير كلمة السر فورًا.')
            ->action('تسجيل الدخول', url('/login'));
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
