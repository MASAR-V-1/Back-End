<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {

            // لو سوبر ادمن، استخدم رابط Breeze العادي (web)
            if ($notifiable->isSuperAdmin()) {
                return url(route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->email,
                ], false));
            }

            // غير ذلك (org_admin / employee) - رابط الفرونت إند
            return config('app.frontend_url') . 'reset-password?token=' . $token . '&email=' . urlencode($notifiable->email);
        });
        // VerifyEmail (الجديد)
        VerifyEmail::createUrlUsing(function ($notifiable) {

            if ($notifiable->isSuperAdmin()) {
                // سوبر ادمن: رابط Breeze الأصلي
                return URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ]
                );
            }

            // غير ذلك: رابط API الجديد (يرجع JSON فعليًا، بدون redirect لـ Breeze)
            $apiVerifyUrl = URL::temporarySignedRoute(
                'api.verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            return rtrim(config('app.frontend_url'), '/') . '/verify-email?verify_url=' . urlencode($apiVerifyUrl);
            // return urlencode($apiVerifyUrl);
        });
    }
}
