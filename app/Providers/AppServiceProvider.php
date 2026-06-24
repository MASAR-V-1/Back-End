<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {

            // لو سوبر ادمن، استخدم رابط Breeze العادي (web)
            if ($notifiable->isSuperAdmin()) {
                return url(route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->email,
                ], false));
            }

            // غير ذلك (org_admin / employee) - رابط الفرونت إند
            return config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($notifiable->email);
        });
    }
}
