<?php

namespace App\Listeners;

use App\Events\ChannelExportReadyEvent;
use App\Notifications\ChannelExportReadyNotification;

class ChannelExportReadyListener
{
    public function __construct()
    {
        //
    }

    public function handle(ChannelExportReadyEvent $event): void
    {
        $event->user->notify(
            new ChannelExportReadyNotification($event->channel, $event->filename)
        );
    }
}