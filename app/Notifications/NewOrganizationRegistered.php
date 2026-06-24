<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrganizationRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Organization $organization)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('طلب تسجيل مؤسسة جديدة')
            ->line('قامت مؤسسة جديدة بالتسجيل وتحتاج لمراجعتك.')
            ->line('اسم المؤسسة: ' . $this->organization->name)
            ->line('الإيميل: ' . $this->organization->email)
            ->action('مراجعة الطلب', url('/dashboard/super-admin/organizations'))
            ->line('شكرًا لاستخدامك المنصة.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    // البيانات الي رح تخزن بجدول notifications (للعرض بالـ dashboard)
    public function toArray(object $notifiable): array
    {
        return [
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'message' => 'مؤسسة جديدة "' . $this->organization->name . '" بانتظار المراجعة.',
        ];
    }
}
