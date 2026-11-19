<?php

namespace Tests\Concerns;

use App\Models\Channel;
use App\Models\Invitation;
use App\Models\Member;
use App\Models\SharedDay;
use App\Models\TimeEntry;
use App\Models\User;

/**
 * Small builders so each test reads as a scenario instead of setup noise.
 */
trait BuildsTrackerData
{
    protected function channelOwnedBy(User $owner, array $attributes = []): Channel
    {
        return Channel::factory()->create(['user_id' => $owner->id, ...$attributes]);
    }

    protected function addMember(Channel $channel, User $user): Member
    {
        return Member::create(['channel_id' => $channel->id, 'user_id' => $user->id]);
    }

    protected function entryFor(User $user, array $attributes = []): TimeEntry
    {
        return TimeEntry::create([
            'user_id'    => $user->id,
            'label'      => 'Deep work',
            'date'       => '2026-01-15',
            'start_time' => '09:00',
            'end_time'   => '10:30',
            ...$attributes,
        ]);
    }

    /** Share one entry of $sharer's on the given date into $channel. */
    protected function shareDay(Channel $channel, User $sharer, string $date = '2026-01-15'): SharedDay
    {
        $entry = $this->entryFor($sharer, ['date' => $date]);

        $sharedDay = SharedDay::create([
            'channel_id' => $channel->id,
            'user_id'    => $sharer->id,
            'date'       => $date,
        ]);
        $sharedDay->entries()->create(['time_entry_id' => $entry->id]);

        return $sharedDay;
    }

    protected function pendingInvitation(Channel $channel, User $invited): Invitation
    {
        return Invitation::create([
            'channel_id'    => $channel->id,
            'invited_id'    => $invited->id,
            'invited_by_id' => $channel->user_id,
            'status'        => 'pending',
        ]);
    }
}
