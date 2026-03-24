<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\{Channel, Invitation, User};

class InvitationPolicy
{
    public function viewAny(User $user, Channel $channel): bool
    {
        return $channel->isOwner($user);
    }
    public function create(User $user, Channel $channel): bool
    {
        return $channel->isOwner($user);
    }

    public function accept(User $user, Invitation $invitation): Response
    {
        if ($invitation->status !== 'pending') {
            return Response::deny('This invitation is no longer pending.');
        }

        return $user->id === $invitation->invited_id
            ? Response::allow()
            : Response::deny('You are not the invited user for this invitation.');
    }

    public function decline(User $user, Invitation $invitation): Response
    {
        if ($invitation->status !== 'pending') {
            return Response::deny('This invitation is no longer pending.');
        }

        return $user->id === $invitation->invited_id
            ? Response::allow()
            : Response::deny('You are not the invited user for this invitation.');
    }

    public function cancel(User $user, Invitation $invitation): Response
    {
        if ($invitation->status !== 'pending') {
            return Response::deny('Only pending invitations can be cancelled.');
        }

        return $user->id === $invitation->invited_by_id
            ? Response::allow()
            : Response::deny('You are not allowed to cancel this invitation.');
    }
}
