<?php

namespace App\Policies;
use App\Models\{Channel, User};

class ChannelPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Channel $channel): bool
    {
            return $channel->isOwnerOrMember($user);
    }

    public function create(User $user): bool
    {
        return true;
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
