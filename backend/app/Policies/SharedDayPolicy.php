<?php

namespace App\Policies;

use App\Models\{Channel, SharedDay, User};

class SharedDayPolicy
{
    /**
     * Only a channel's owner can share days into it.
     * (Viewing shared days is governed by ChannelPolicy::view.)
     */
    public function create(User $user, Channel $channel): bool
    {
        return $channel->isOwner($user);
    }

    public function delete(User $user, SharedDay $sharedDay): bool
    {
        return $user->id === $sharedDay->user_id;
    }
}
