<?php

namespace Tests\Feature;

use App\Models\SharedDay;
use App\Models\User;
use App\Notifications\NewSharedDayNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class SharedDayTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    private User $owner;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->member = User::factory()->create();
    }

    public function test_the_owner_can_share_a_day_into_several_channels_and_members_are_notified(): void
    {
        Notification::fake();
        $a = $this->channelOwnedBy($this->owner);
        $b = $this->channelOwnedBy($this->owner);
        $this->addMember($a, $this->member);
        $entry = $this->entryFor($this->owner);
        Sanctum::actingAs($this->owner);

        $this->postJson('/api/shared-days', [
            'date' => '2026-01-15', 'channel_ids' => [$a->id, $b->id], 'entry_ids' => [$entry->id],
        ])->assertOk()->assertJsonCount(2, 'data');

        $this->assertSame(2, SharedDay::count());
        $this->assertDatabaseHas('shared_day_entries', ['time_entry_id' => $entry->id]);
        Notification::assertSentTo($this->member, NewSharedDayNotification::class);
    }

    public function test_sharing_the_same_day_twice_does_not_duplicate_it(): void
    {
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $entry = $this->entryFor($this->owner);
        Sanctum::actingAs($this->owner);
        $payload = ['date' => '2026-01-15', 'channel_ids' => [$channel->id], 'entry_ids' => [$entry->id]];

        $this->postJson('/api/shared-days', $payload)->assertOk();
        $this->postJson('/api/shared-days', $payload)->assertOk();

        $this->assertSame(1, SharedDay::count());
        $this->assertDatabaseCount('shared_day_entries', 1);
    }

    public function test_only_a_channel_owner_can_share_days_into_it(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $this->addMember($channel, $this->member);
        $entry = $this->entryFor($this->member);
        Sanctum::actingAs($this->member);

        $this->postJson('/api/shared-days', [
            'date' => '2026-01-15', 'channel_ids' => [$channel->id], 'entry_ids' => [$entry->id],
        ])->assertForbidden();

        $this->assertSame(0, SharedDay::count());
    }

    public function test_a_user_cannot_share_entries_that_are_not_theirs_or_a_day_without_entries(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $foreign = $this->entryFor($this->member);
        Sanctum::actingAs($this->owner);

        $this->postJson('/api/shared-days', [
            'date' => '2026-01-15', 'channel_ids' => [$channel->id], 'entry_ids' => [$foreign->id],
        ])->assertStatus(422);

        $this->postJson('/api/shared-days', [
            'date' => '2026-06-01', 'channel_ids' => [$channel->id], 'entry_ids' => [$foreign->id],
        ])->assertStatus(422)->assertJsonValidationErrors('date');
    }

    public function test_members_can_list_and_open_shared_days_but_outsiders_cannot(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $this->addMember($channel, $this->member);
        $sharedDay = $this->shareDay($channel, $this->owner);

        foreach ([$this->owner, $this->member] as $allowed) {
            Sanctum::actingAs($allowed);
            $this->getJson("/api/channels/{$channel->id}/shared-days")->assertOk()->assertJsonCount(1, 'data');
            $this->getJson("/api/channels/{$channel->id}/shared-days/{$sharedDay->id}")
                ->assertOk()
                ->assertJsonPath('data.entries_count', 1)
                ->assertJsonPath('data.total_time', ['hours' => 1, 'minutes' => 30]);
        }

        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/channels/{$channel->id}/shared-days")->assertForbidden();
        $this->getJson("/api/channels/{$channel->id}/shared-days/{$sharedDay->id}")->assertForbidden();
    }

    public function test_a_shared_day_cannot_be_read_through_a_different_channel(): void
    {
        $mine = $this->channelOwnedBy($this->member);
        $secret = $this->channelOwnedBy($this->owner);
        $secretDay = $this->shareDay($secret, $this->owner);
        Sanctum::actingAs($this->member);

        // The member is allowed in $mine, but the shared day belongs to $secret.
        $this->getJson("/api/channels/{$mine->id}/shared-days/{$secretDay->id}")->assertNotFound();
        $this->deleteJson("/api/channels/{$mine->id}/shared-days/{$secretDay->id}")->assertNotFound();
    }

    public function test_only_the_user_who_shared_the_day_can_delete_it(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $this->addMember($channel, $this->member);
        $sharedDay = $this->shareDay($channel, $this->owner);

        Sanctum::actingAs($this->member);
        $this->deleteJson("/api/channels/{$channel->id}/shared-days/{$sharedDay->id}")->assertForbidden();

        Sanctum::actingAs($this->owner);
        $this->deleteJson("/api/channels/{$channel->id}/shared-days/{$sharedDay->id}")->assertOk();
        $this->assertModelMissing($sharedDay);
    }

    public function test_my_shared_days_are_grouped_by_date(): void
    {
        $a = $this->channelOwnedBy($this->owner, ['name' => 'Alpha']);
        $b = $this->channelOwnedBy($this->owner, ['name' => 'Beta']);
        $this->shareDay($a, $this->owner, '2026-01-15');
        $this->shareDay($b, $this->owner, '2026-01-15');
        $this->shareDay($a, $this->owner, '2026-01-16');
        Sanctum::actingAs($this->owner);

        $response = $this->getJson('/api/me/shared-days')->assertOk();

        $this->assertCount(2, $response->json());
        $firstDay = collect($response->json())->first(fn ($day) => str_starts_with($day['date'], '2026-01-15'));
        $this->assertCount(2, $firstDay['channels']);
    }
}
