<?php

namespace App\Listeners;

use App\Events\NewSharedDayEvent;
use App\Notifications\NewSharedDayNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NewSharedDayListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewSharedDayEvent $event): void
    {
        $shared_day = $event->sharedDay->load(['channel.members.user']);
        $members = $shared_day->channel->members->map(fn($member)=> $member->user);
        Notification::send($members, new NewSharedDayNotification($shared_day));
    }
}
