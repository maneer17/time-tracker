<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use DomainException;

class InvitationStatusService
{
    public function changeStatus(Invitation $invitation, string $status, User $actor): void
    {
        match ($status) {
            'accepted'  => $this->accept($invitation, $actor),
            'declined'  => $this->decline($invitation, $actor),
            'cancelled' => $this->cancel($invitation, $actor),
            default     => throw new InvalidArgumentException('Invalid status value.'),
        };
    }

    public function accept(Invitation $invitation, User $actor): void
    {
        if ($invitation->status !== 'pending') {
            throw new DomainException('Invitation is not pending.');
        }

        DB::transaction(function () use ($invitation, $actor) {
            $invitation->update([
                'status' => 'accepted',
            ]);

            Member::firstOrCreate([
                'user_id'    => $actor->id,
                'channel_id' => $invitation->channel_id,
            ]);
        });
    }

    public function decline(Invitation $invitation, User $actor): void
    {
        if ($invitation->status !== 'pending') {
            throw new DomainException('Invitation is not pending.');
        }

        $invitation->update([
            'status' => 'declined',
        ]);
    }

    public function cancel(Invitation $invitation, User $actor): void
    {
        if ($invitation->status !== 'pending') {
            throw new DomainException('Invitation is not pending.');
        }

        $invitation->update([
            'status' => 'cancelled',
        ]);
    }
}
