<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Invitation;
use App\Models\TimeEntry;
use App\Observers\InvitationObserver;
use App\Observers\TimeEntryObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
/**
 * Register any application services.
 */
    public function register(): void
    {
        
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
