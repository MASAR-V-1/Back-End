<?php

namespace Modules\ITAdmin\app\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ITAdmin\app\Providers\EventServiceProvider;
use Modules\ITAdmin\app\Providers\RouteServiceProvider;

class ITAdminServiceProvider extends ServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'ITAdmin';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'itadmin';


    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        // إجبار لارافيل على تسجيل وتفعيل كافة المزودين الفرعيين للموديول وضمان قراءتهم
        foreach ($this->providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * تسجيل الخدمات داخل حاوية لارافيل.
     */
    public function register(): void
    {
        //
    }

    /**
     * Define module schedules.
     *
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
