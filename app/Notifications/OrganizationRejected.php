<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Organization $organization, public string $reason)
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
            ->subject('بخصوص طلب تسجيل مؤسستكم')
            ->line('نأسف لإعلامكم بأنه تم رفض طلب تسجيل مؤسسة "' . $this->organization->name . '".')
            ->line('سبب الرفض: ' . $this->reason)
            ->line('يمكنكم التسجيل من جديد بعد تعديل البيانات المطلوبة.')
            ->action('تسجيل من جديد', url('/register'))
            ->line('شكرًا لتفهمكم.');
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
