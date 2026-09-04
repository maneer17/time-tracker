<?php

namespace App\Policies;


use App\Models\{Channel, SharedDay, User};

class SharedDayPolicy
{


    public function viewAny(User $user, Channel $channel): bool
    {
        return $channel->isOwnerOrMember($user);
    }

    public function view(User $user, Channel $channel): bool
    {
        return $channel->isOwnerOrMember($user);
    }
    public function create(User $user, Channel $channel): bool
    {   
        return $channel->isOwner($user) ;   
    }

    public function delete(User $user, SharedDay $sharedDay): bool
    {
        return $user->id === $sharedDay->user_id;
    }


}
