<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{Invitation, TimeEntry};
use App\Observers\{InvitationObserver, TimeEntryObserver};
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
/**
 * Register any application services.
 */
/**
 * Register any application services.
 */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TimeEntry::observe(TimeEntryObserver::class);
        Invitation::observe(InvitationObserver::class);
    }
}
