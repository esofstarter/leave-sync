<?php

namespace App\Applications\LeaveRequest\Providers;

use App\Applications\LeaveRequest\Repositories\LeaveRequestRepository;
use App\Applications\LeaveRequest\Repositories\LeaveRequestRepositoryInterface;
use App\Applications\LeaveRequest\Services\LeaveRequestService;
use App\Applications\LeaveRequest\Services\LeaveRequestServiceInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LeaveRequestServiceProvider extends ServiceProvider
{
    /**
     * Set the service provider namespace
     *
     */
    protected $namespace = 'App\Applications\LeaveRequest';
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
        $this->app->bind(LeaveRequestRepositoryInterface::class, LeaveRequestRepository::class);
        $this->app->bind(LeaveRequestServiceInterface::class, LeaveRequestService::class);
    }

    public function map()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/LeaveRequest/api.php'));
    }

}
