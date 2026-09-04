<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        Schedule::command('auth:clear-resets')->daily()->onOneServer();
        Schedule::command('notifications:prune', ['--days=30'])->daily()->onOneServer();
        Schedule::command('queue:prune-failed', ['--hours=168'])->weekly()->onOneServer();
        // ensure task run on one server
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
