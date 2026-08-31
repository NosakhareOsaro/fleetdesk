<?php

namespace App\Providers;

use App\Contracts\CalendarSyncService;
use App\Services\Graph\MicrosoftGraphCalendarSyncService;
use App\Services\Graph\NullCalendarSyncService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CalendarSyncService::class,
            $this->app->environment('testing') ? NullCalendarSyncService::class : MicrosoftGraphCalendarSyncService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
