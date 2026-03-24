<?php

namespace App\Observers;

use App\Models\TimeEntry;

class TimeEntryObserver
{
    public function creating(TimeEntry $entry): void
    {
        $entry->duration_minutes = $entry->start_time->diffInMinutes($entry->end_time);
    }

    public function updating(TimeEntry $entry): void
    {
        $entry->duration_minutes = $entry->start_time->diffInMinutes($entry->end_time);
    }
}
