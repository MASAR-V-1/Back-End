<?php

namespace Modules\ITAdmin\app\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'ITAdmin';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        // استخدام base_path لضمان الوصول الصارم لملف المسارات بدون الاعتماد على دوال الحزمة الإضافية
        $webRouteFile = base_path('Modules/ITAdmin/routes/web.php');

        if (file_exists($webRouteFile)) {
            Route::middleware('web')
                ->group($webRouteFile);
        }
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        // استخدام base_path لضمان الوصول الصارم لملف المسارات بدون الاعتماد على دوال الحزمة الإضافية
        $apiRouteFile = base_path('Modules/ITAdmin/routes/api.php');

        if (file_exists($apiRouteFile)) {
            Route::middleware('api')
                ->prefix('api') // جعل جميع المسارات تبدأ بـ /api/
                ->group($apiRouteFile);
        }
    }
}
