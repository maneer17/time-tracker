<?php

namespace Tests\Unit;

use App\Models\TimeEntry;
use Tests\TestCase;

class TimeEntryTest extends TestCase
{
    public function test_time_taken_is_split_into_hours_and_minutes(): void
    {
        $entry = new TimeEntry(['start_time' => '09:15', 'end_time' => '11:50']);

        $this->assertSame(['hours' => 2, 'minutes' => 35], $entry->time_taken);
    }

    public function test_the_validation_rules_require_an_end_after_the_start(): void
    {
        $rules = TimeEntry::baseRules();

        $this->assertStringContainsString('after:start_time', $rules['end_time']);
        $this->assertStringContainsString('required', $rules['label']);
    }
}
