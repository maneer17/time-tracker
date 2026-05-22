<?php

namespace App\Listeners;

use App\Events\AcceptedInvitationEvent;
use App\Notifications\AcceptedInvitationNotification;

class AcceptedInvitationListener
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
    public function handle(AcceptedInvitationEvent $event): void
    {
        $invitation = $event->invitation->load(['invitedUser', 'invitedBy', 'channel']);

        $invitation->invitedBy->notify(new AcceptedInvitationNotification($invitation));
    }
}
