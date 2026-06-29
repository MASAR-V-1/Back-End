<?php

namespace Modules\ITAdmin\app\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ITAdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * إنشاء نسخة جديدة من الرسالة وتمرير البيانات إليها.
     * نمرر الاسم، البريد، ورابط التفعيل الآمن المتوجه إلى React.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $invitationUrl
    ) {
    }

    /**
     * ضبط عنوان الرسالة والـ Envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎯 منصة مسار: دعوة انضمام وتعيين كمسؤول نظام (IT Admin)',
        );
    }

    /**
     * تحديد محتوى الرسالة وتنسيق الـ HTML الموجه للموظف.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='direction: rtl; font-family: sans-serif; padding: 20px; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px;'>
                    <h2 style='color: #1e40af;'>أهلاً بك يا {$this->name} في منصة مَسَار (MASAR)</h2>
                    <p>لقد قامت مؤسستك بدعوتك للانضمام وتفويضك كـ <strong>مسؤول نظام (IT Admin)</strong> لإدارة البنية التقنية داخل المؤسسة.</p>
                    <hr style='border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p>لحماية حسابك وبيانات المنظمة، يرجى الضغط على الرابط الآمن أدناه لإنشاء كلمة المرور الخاصة بك وتفعيل الحساب مباشرة على لوحة التحكم:</p>

                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$this->invitationUrl}' style='background: #1e40af; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>اضغط هنا لتفعيل حسابك وتعيين كلمة المرور</a>
                    </p>

                    <p>معلومات الحساب المرتبط بالدعوة:</p>
                    <ul>
                        <li><strong>البريد الإلكتروني المدعو:</strong> {$this->email}</li>
                    </ul>

                    <p style='color: #d9534f;'>⚠️ <strong>ملاحظة أمنية:</strong> هذا الرابط الآمن مخصص ومقفل لحسابك وصالح للاستخدام لمرة واحدة فقط، وينتهي تلقائياً بعد 24 ساعة من تاريخ الإرسال.</p>
                    <br>
                    <p>بالتوفيق،<br>فريق تطوير نظام مَسَار</p>
                </div>
            "
        );
    }
}
