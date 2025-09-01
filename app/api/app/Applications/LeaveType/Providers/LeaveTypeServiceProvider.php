<?php

namespace App\Applications\LeaveType\Providers;

use App\Applications\LeaveType\Repositories\LeaveTypeRepository;
use App\Applications\LeaveType\Repositories\LeaveTypeRepositoryInterface;
use App\Applications\LeaveType\Services\LeaveTypeService;
use App\Applications\LeaveType\Services\LeaveTypeServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LeaveTypeServiceProvider extends ServiceProvider
{
    /**
     * Set the service provider namespace
     *
     */
    protected $namespace = 'App\Applications\LeaveType';
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
        $this->app->bind(LeaveTypeRepositoryInterface::class, LeaveTypeRepository::class);
        $this->app->bind(LeaveTypeServiceInterface::class, LeaveTypeService::class);
    }

    public function map()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/LeaveType/api.php'));
    }

}
