<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['auth:sanctum'],
        'prefix'     => 'api',
        ]);
        // Broadcast::routes(); uses web middlware by default therefore uses session auth not token
        require base_path('routes/channels.php');
    }
}
