<?php

namespace App\Listeners;

use App\Events\InvitationSent;
use App\Notifications\NewInvitation;


class NewInvitationListener
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
    public function handle(InvitationSent $event): void
    {
        $invitation = $event->invitation->load(['invitedUser', 'invitedBy', 'channel']);

        $invitation->invitedUser->notify(new NewInvitation($invitation));
    }
}
