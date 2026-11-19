<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    public function test_a_user_can_create_a_channel_and_becomes_its_owner(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/channels', ['name' => 'Backend team', 'description' => 'Daily logs'])
            ->assertCreated()->assertJsonPath('data.name', 'Backend team');

        $this->assertTrue(Channel::first()->isOwner($user));
    }

    public function test_a_channel_requires_a_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/channels', ['description' => 'no name'])
            ->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_the_channel_list_only_contains_channels_the_user_owns_or_joined(): void
    {
        $me = User::factory()->create();
        $stranger = User::factory()->create();
        $owned = $this->channelOwnedBy($me);
        $joined = $this->channelOwnedBy($stranger);
        $this->addMember($joined, $me);
        $unrelated = $this->channelOwnedBy($stranger);
        Sanctum::actingAs($me);

        $ids = fn (string $query = '') => collect($this->getJson("/api/channels{$query}")->json('data'))->pluck('id')->sort()->values()->all();

        $this->assertSame([$owned->id, $joined->id], $ids(), 'default list');
        $this->assertSame([$owned->id], $ids('?type=owned'));
        $this->assertSame([$joined->id], $ids('?type=joined'));
        $this->assertNotContains($unrelated->id, $ids());
    }

    public function test_an_outsider_cannot_view_a_channel_but_owner_and_members_can(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $channel = $this->channelOwnedBy($owner);
        $this->addMember($channel, $member);

        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/channels/{$channel->id}")->assertForbidden();

        Sanctum::actingAs($member);
        $this->getJson("/api/channels/{$channel->id}")->assertOk()->assertJsonPath('data.id', $channel->id);

        Sanctum::actingAs($owner);
        $this->getJson("/api/channels/{$channel->id}")->assertOk();
    }

    public function test_the_owner_can_update_a_channel(): void
    {
        $owner = User::factory()->create();
        $channel = $this->channelOwnedBy($owner, ['name' => 'Old', 'description' => 'Old description']);
        Sanctum::actingAs($owner);

        $this->putJson("/api/channels/{$channel->id}", ['name' => 'New', 'description' => 'Fresh'])
            ->assertOk()->assertJsonPath('data.name', 'New');

        $this->assertSame('Fresh', $channel->fresh()->description);
    }

    public function test_only_the_owner_can_update_or_delete_a_channel(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $channel = $this->channelOwnedBy($owner, ['name' => 'Original']);
        $this->addMember($channel, $member);

        foreach ([$member, User::factory()->create()] as $intruder) {
            Sanctum::actingAs($intruder);
            $this->putJson("/api/channels/{$channel->id}", ['name' => 'Hijacked'])->assertForbidden();
            $this->deleteJson("/api/channels/{$channel->id}")->assertForbidden();
        }

        $this->assertSame('Original', $channel->fresh()->name);
    }

    public function test_the_owner_can_delete_a_channel(): void
    {
        $owner = User::factory()->create();
        $channel = $this->channelOwnedBy($owner);
        Sanctum::actingAs($owner);

        $this->deleteJson("/api/channels/{$channel->id}")->assertOk();

        $this->assertModelMissing($channel);
    }
}
