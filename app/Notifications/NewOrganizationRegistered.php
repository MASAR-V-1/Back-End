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
        // إذا كانت المنظمة قد قامت بتحديث بياناتها بعد طلب التعديل
        if ($this->organization->status == Organization::STATUS_NEEDS_CHANGES) {
            return (new MailMessage)
                ->subject('تحديث بيانات: إعادة تقديم طلب تسجيل مؤسسة')
                ->greeting('مرحباً Super Admin،')
                ->line('قام مالك المؤسسة بتحديث البيانات المطلوبة وإعادة إرسال الطلب للمراجعة والتدقيق مرة أخرى.')
                ->line('اسم المؤسسة: ' . $this->organization->name)
                ->line('البريد الإلكتروني: ' . $this->organization->email)
                ->action('مراجعة التعديلات والطلب', route('super-admin.organizations.show', $this->organization))
                ->line('شكرًا لاستخدامك المنصة.');
        }

        // الحالة الافتراضية: طلب تسجيل جديد لأول مرة
        return (new MailMessage)
            ->subject('طلب تسجيل مؤسسة جديدة')
            ->greeting('مرحباً Super Admin،')
            ->line('قامت مؤسسة جديدة بالتسجيل في النظام وتحتاج إلى مراجعتك واعتمادها.')
            ->line('اسم المؤسسة: ' . $this->organization->name)
            ->line('البريد الإلكتروني: ' . $this->organization->email)
            ->action('فحص ومراجعة الطلب', route('super-admin.organizations.show', $this->organization))
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
