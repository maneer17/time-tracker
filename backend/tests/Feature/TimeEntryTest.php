<?php

namespace Tests\Feature;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class TimeEntryTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    public function test_a_user_can_log_a_time_entry_and_the_duration_is_calculated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/time-entries', [
            'label' => 'Writing tests', 'start_time' => '09:15', 'end_time' => '11:45',
        ])->assertCreated()->assertJsonPath('data.label', 'Writing tests')
          ->assertJsonPath('data.time_taken', ['hours' => 2, 'minutes' => 30]);

        $entry = $user->time_entries()->first();
        $this->assertSame(150, $entry->duration_minutes);
        $this->assertSame(today()->toDateString(), $entry->date->toDateString(), 'date defaults to today');
    }

    public function test_the_end_time_must_be_after_the_start_time(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/time-entries', [
            'label' => 'Backwards', 'start_time' => '12:00', 'end_time' => '11:00',
        ])->assertStatus(422)->assertJsonValidationErrors('end_time');
    }

    public function test_a_user_only_sees_their_own_entries(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $mine = $this->entryFor($me, ['date' => today()->toDateString(), 'label' => 'Mine']);
        $this->entryFor($other, ['date' => today()->toDateString(), 'label' => 'Theirs']);
        Sanctum::actingAs($me);

        $response = $this->getJson('/api/time-entries')->assertOk();

        $this->assertSame([$mine->id], collect($response->json('data'))->pluck('id')->all());
    }

    public function test_entries_can_be_filtered_by_date_and_label(): void
    {
        $user = User::factory()->create();
        $this->entryFor($user, ['date' => '2026-02-01', 'label' => 'Design review']);
        $this->entryFor($user, ['date' => '2026-02-01', 'label' => 'Coding']);
        $this->entryFor($user, ['date' => '2026-02-02', 'label' => 'Design sync']);
        Sanctum::actingAs($user);

        $labels = collect($this->getJson('/api/time-entries?date=2026-02-01&search=design')->json('data'))->pluck('label');

        $this->assertSame(['Design review'], $labels->all());
    }

    public function test_the_owner_can_update_and_delete_an_entry(): void
    {
        $user = User::factory()->create();
        $entry = $this->entryFor($user);
        Sanctum::actingAs($user);

        $this->putJson("/api/time-entries/{$entry->id}", ['label' => 'Renamed', 'end_time' => '12:00'])
            ->assertOk()->assertJsonPath('data.label', 'Renamed');
        $this->assertSame(180, $entry->fresh()->duration_minutes, 'duration is recalculated on update');

        $this->deleteJson("/api/time-entries/{$entry->id}")->assertOk();
        $this->assertModelMissing($entry);
    }

    public function test_a_user_cannot_view_update_or_delete_someone_elses_entry(): void
    {
        $owner = User::factory()->create();
        $entry = $this->entryFor($owner, ['label' => 'Private']);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/time-entries/{$entry->id}")->assertForbidden();
        $this->putJson("/api/time-entries/{$entry->id}", ['label' => 'Hacked'])->assertForbidden();
        $this->deleteJson("/api/time-entries/{$entry->id}")->assertForbidden();

        $this->assertSame('Private', $entry->fresh()->label);
    }

    public function test_the_history_lists_each_day_with_entries_once(): void
    {
        $user = User::factory()->create();
        $this->entryFor($user, ['date' => '2026-03-01']);
        $this->entryFor($user, ['date' => '2026-03-01', 'start_time' => '13:00', 'end_time' => '14:00']);
        $this->entryFor($user, ['date' => '2026-03-02']);
        Sanctum::actingAs($user);

        $this->getJson('/api/time-entries?history=1')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
