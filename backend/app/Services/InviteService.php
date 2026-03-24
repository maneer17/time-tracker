<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class InviteService
{
    public function invite(string $identifier, Channel $channel): Invitation
    {
        $user = $this->findUser($identifier);

        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => ['User not found for the given identifier.'],
            ]);
        }

        $this->ensureNotOwner($user, $channel);
        $this->ensureNotMember($user, $channel);

        return $this->updateOrCreate($user, $channel);
    }

    private function findUser(string $identifier): ?User
    {
        return User::query()
            ->where('email', $identifier)
            ->orWhere('name', $identifier)
            ->first();
    }

    private function ensureNotOwner(User $user, Channel $channel): void
    {
        if ($channel->user_id === $user->id) {
            throw ValidationException::withMessages([
                'identifier' => ['You cannot invite the channel owner.'],
            ]);
        }
    }

    private function ensureNotMember(User $user, Channel $channel): void
    {
        if ($channel->members()->where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'identifier' => ['This user is already a member of this channel.'],
            ]);
        }
    }

    private function updateOrCreate(User $user, Channel $channel): Invitation
    {
        $existing = Invitation::where('channel_id', $channel->id)
            ->where('invited_id', $user->id)
            ->first();

        if ($existing && in_array($existing->status, ['declined', 'cancelled'], true)) {
            $existing->update([
                'status' => 'pending',
            ]);

            return $existing->refresh();
        }

        if ($existing) {
            throw ValidationException::withMessages([
                'identifier' => ['User already has a pending invitation to this channel.'],
            ]);
        }

        return Invitation::create([
            'channel_id'    => $channel->id,
            'invited_id'    => $user->id,
            'invited_by_id' => $channel->user_id,
            'status'        => 'pending',
        ]);
    }
}
