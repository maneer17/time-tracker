<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;

class TimeEntryPolicy
{
    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }

    public function update(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }

    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }
}
