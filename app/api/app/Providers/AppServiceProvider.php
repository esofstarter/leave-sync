<?php

namespace App\Providers;

use App\Applications\Pagination\StarterPaginator;
use App\Applications\User\Model\User;
use App\Applications\LeaveRequest\Services\RedmineTimeLoggerServiceInterface;
use App\Applications\LeaveRequest\Services\RedmineTimeLoggerService;
use App\Observers\UserObserver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->alias(StarterPaginator::class, LengthAwarePaginator::class); // Eloquent uses the class instead of the contract 🤔
        $this->app->alias(StarterPaginator::class, LengthAwarePaginatorContract::class);
        
        // Bind Redmine time logger interface to implementation
        $this->app->bind(RedmineTimeLoggerServiceInterface::class, RedmineTimeLoggerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        User::observe(UserObserver::class);
    }
}
