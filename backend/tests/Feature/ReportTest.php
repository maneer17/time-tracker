<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    private function seedEntries(User $user): void
    {
        $this->entryFor($user, ['date' => '2026-03-02', 'label' => 'Coding', 'start_time' => '09:00', 'end_time' => '12:00']); // 180
        $this->entryFor($user, ['date' => '2026-03-03', 'label' => 'Coding', 'start_time' => '09:00', 'end_time' => '10:00']); // 60
        $this->entryFor($user, ['date' => '2026-03-03', 'label' => 'Meetings', 'start_time' => '10:00', 'end_time' => '11:00']); // 60
        $this->entryFor($user, ['date' => '2026-04-01', 'label' => 'Out of range']);
    }

    public function test_a_report_summarises_entries_inside_the_date_range(): void
    {
        $user = User::factory()->create();
        $this->seedEntries($user);
        Sanctum::actingAs($user);

        $report = $this->getJson('/api/reports?from=2026-03-01&to=2026-03-31')->assertOk();

        $report->assertJsonPath('quick_stats.total_entries', 3)
            ->assertJsonPath('quick_stats.total_hours', 5)
            ->assertJsonPath('quick_stats.total_minutes', 0)
            ->assertJsonPath('quick_stats.total_days', 2)            // two distinct days, not "days the rows were created"
            ->assertJsonPath('quick_stats.avg_hours_per_day', 2)
            ->assertJsonPath('quick_stats.avg_minutes_per_day', 30);

        $labels = collect($report->json('most_used_labels'));
        $this->assertSame('Coding', $labels->first()['label']);
        $this->assertEquals(80.0, $labels->first()['percentage']); // 240 of 300 minutes
        $this->assertNotContains('Out of range', $labels->pluck('label')->all());
    }

    public function test_a_report_never_includes_other_users_entries(): void
    {
        $me = User::factory()->create();
        $this->entryFor($me, ['date' => '2026-03-02']);
        $this->seedEntries(User::factory()->create());
        Sanctum::actingAs($me);

        $this->getJson('/api/reports?from=2026-03-01&to=2026-03-31')
            ->assertOk()->assertJsonPath('quick_stats.total_entries', 1);
    }

    public function test_reports_are_cached_until_refreshed(): void
    {
        $user = User::factory()->create();
        $this->entryFor($user, ['date' => '2026-03-02']);
        Sanctum::actingAs($user);
        $url = '/api/reports?from=2026-03-01&to=2026-03-31';

        $this->getJson($url)->assertJsonPath('quick_stats.total_entries', 1);
        $this->entryFor($user, ['date' => '2026-03-04']);

        $this->getJson($url)->assertJsonPath('quick_stats.total_entries', 1);
        $this->getJson($url.'&refresh=1')->assertJsonPath('quick_stats.total_entries', 2);
    }

    public function test_the_range_must_be_valid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/reports?from=2026-03-31&to=2026-03-01')
            ->assertStatus(422)->assertJsonValidationErrors('to');
    }
}
