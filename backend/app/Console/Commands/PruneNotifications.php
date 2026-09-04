<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class PruneNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:prune {--days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove notifications older than n days (30 days be default) that are not read';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $deleted = DB::table('notifications')
        ->where('created_at', '<',  now()->subDays($days))
        ->where('read_at', '!=', null)
        ->delete();
    $this->info("Deleted {$deleted} notifications older than {$days} days.");    
    }
}
