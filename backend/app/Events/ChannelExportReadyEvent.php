<?php

namespace App\Events;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelExportReadyEvent
{
    use Dispatchable, SerializesModels;

    // this Event's only job is carrying data to the Listener
    // actual Reverb broadcasting happens inside the Notification's
    // toBroadcast() — no ShouldBroadcast needed here
    public function __construct(
        public User $user,
        public string $filename,
        public Channel $channel,
    ) {}
}