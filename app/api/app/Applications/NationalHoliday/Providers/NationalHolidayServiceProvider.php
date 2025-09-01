<?php

namespace App\Applications\NationalHoliday\Providers;

use App\Applications\NationalHoliday\Repositories\NationalHolidayRepository;
use App\Applications\NationalHoliday\Repositories\NationalHolidayRepositoryInterface;
use App\Applications\NationalHoliday\Services\NationalHolidayService;
use App\Applications\NationalHoliday\Services\NationalHolidayServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class NationalHolidayServiceProvider extends ServiceProvider
{
    /**
     * Set the service provider namespace
     *
     */
    protected $namespace = 'App\Applications\NationalHoliday';
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->routesAreCached()) {
         //  This is already done in the main RouteServiceProvider so not needed here
        } else {

            $this->app->call([$this, 'map']);

            $this->app->booted(function () {
                $this->app['router']->getRoutes()->refreshNameLookups();
                $this->app['router']->getRoutes()->refreshActionLookups();
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NationalHolidayRepositoryInterface::class, NationalHolidayRepository::class);
        $this->app->bind(NationalHolidayServiceInterface::class, NationalHolidayService::class);
    }

    public function map()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/NationalHoliday/api.php'));
    }

}
