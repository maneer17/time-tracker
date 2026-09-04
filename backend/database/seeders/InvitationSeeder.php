<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = Channel::all();

        $channels->each(function (Channel $channel) {
            // Pick some users that are not the channel owner
            $users = User::where('id', '!=', $channel->user_id)
                ->inRandomOrder()
                ->take(3)
                ->get();

            $users->each(function (User $user) use ($channel) {
                $invitation = Invitation::where('channel_id', $channel->id)
                    ->where('invited_id', $user->id)
                    ->first();

                if ($invitation) {
                    // If already a member (accepted), do nothing
                    if ($invitation->status === 'accepted') {
                        return;
                    }

                    // If declined or cancelled (or any non-pending, non-accepted future state),
                    // reset back to pending instead of creating a new row.
                    if ($invitation->status !== 'pending') {
                        $invitation->update([
                            'status' => 'pending',
                        ]);
                    }

                    return;
                }

                // No existing invitation: create a new pending one
                Invitation::create([
                    'channel_id'    => $channel->id,
                    'invited_id'    => $user->id,
                    'invited_by_id' => $channel->user_id,
                    'status'        => 'pending',
                ]);
            });
        });
    }
}
