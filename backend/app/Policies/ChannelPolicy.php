<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;

class ChannelPolicy
{
    /** The owner and joined members can see a channel and everything shared in it. */
    public function view(User $user, Channel $channel): bool
    {
        return $channel->isOwnerOrMember($user);
    }

    public function update(User $user, Channel $channel): bool
    {
        return $channel->isOwner($user);
    }

    public function delete(User $user, Channel $channel): bool
    {
        return $channel->isOwner($user);
    }
}
