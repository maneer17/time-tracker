<?php

namespace App\Observers;

use App\Models\Invitation;
use App\Models\InvitationLog;

class InvitationObserver
{

    public function created(Invitation $invitation): void
    {
        InvitationLog::create([
            'invitation_id' => $invitation->id,
            'user_id'       => $invitation->invited_by_id,
            'action'        => 'sent',
        ]);
    }

    public function updated(Invitation $invitation): void
        {
            if (! $invitation->wasChanged('status')) {
                return;
            }

            if (! in_array($invitation->status, ['accepted', 'declined', 'cancelled'], true)) {
                return;
            }

            InvitationLog::create([
                'invitation_id' => $invitation->id,
                'user_id'       => in_array($invitation->status, ['accepted', 'declined'], true)
                    ? $invitation->invited_id
                    : $invitation->invited_by_id, 
                'action'        => $invitation->status,
            ]);
        }
}
