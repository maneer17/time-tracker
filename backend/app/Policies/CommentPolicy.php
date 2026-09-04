<?php

namespace App\Policies;

use App\Models\{Channel, Comment, User};

class CommentPolicy
{
    public function viewAny(User $user, Channel $channel): bool
    {
        return $channel->isOwnerOrMember($user);
    }

    public function create(User $user, Channel $channel): bool
    {
        return $channel->isOwnerOrMember($user);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment, Channel $channel): bool
    {
        return $comment->user_id === $user->id || $channel->isOwner($user);
    }
}