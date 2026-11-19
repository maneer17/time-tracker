<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use App\Notifications\AcceptedInvitationNotification;
use App\Notifications\NewInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    private User $owner;
    private User $guest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->guest = User::factory()->create(['name' => 'Grace Hopper', 'email' => 'grace@example.com']);
    }

    public function test_the_owner_can_invite_a_user_by_email_and_the_user_is_notified(): void
    {
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        Sanctum::actingAs($this->owner);

        $this->postJson("/api/channels/{$channel->id}/invitations", ['identifier' => 'grace@example.com'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending');

        $invitation = Invitation::firstOrFail();
        $this->assertSame($this->guest->id, $invitation->invited_id);
        $this->assertSame($this->owner->id, $invitation->invited_by_id);
        $this->assertDatabaseHas('invitation_logs', ['invitation_id' => $invitation->id, 'action' => 'sent']);
        Notification::assertSentTo($this->guest, NewInvitation::class);
    }

    public function test_a_user_can_also_be_invited_by_username(): void
    {
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        Sanctum::actingAs($this->owner);

        $this->postJson("/api/channels/{$channel->id}/invitations", ['identifier' => 'Grace Hopper'])
            ->assertCreated();
    }

    public function test_only_the_channel_owner_can_invite_people(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $member = User::factory()->create();
        $this->addMember($channel, $member);

        foreach ([$member, User::factory()->create()] as $user) {
            Sanctum::actingAs($user);
            $this->postJson("/api/channels/{$channel->id}/invitations", ['identifier' => 'grace@example.com'])
                ->assertForbidden();
        }

        $this->assertSame(0, Invitation::count());
    }

    public function test_invalid_invitations_are_rejected(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $existingMember = User::factory()->create(['email' => 'member@example.com']);
        $this->addMember($channel, $existingMember);
        $this->pendingInvitation($channel, $this->guest);
        Sanctum::actingAs($this->owner);

        $cases = [
            'unknown user'        => 'nobody@example.com',
            'the owner themself'  => $this->owner->email,
            'an existing member'  => 'member@example.com',
            'a pending invitee'   => 'grace@example.com',
        ];

        foreach ($cases as $label => $identifier) {
            $this->postJson("/api/channels/{$channel->id}/invitations", ['identifier' => $identifier])
                ->assertStatus(422)
                ->assertJsonValidationErrors('identifier');
        }
    }

    public function test_the_owner_sees_only_pending_invitations_for_their_channel(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $this->pendingInvitation($channel, $this->guest);
        $declined = $this->pendingInvitation($channel, User::factory()->create());
        $declined->update(['status' => 'declined']);

        Sanctum::actingAs($this->owner);
        $this->getJson("/api/channels/{$channel->id}/invitations")->assertOk()->assertJsonCount(1, 'data');

        Sanctum::actingAs($this->guest);
        $this->getJson("/api/channels/{$channel->id}/invitations")->assertForbidden();
    }

    public function test_the_invited_user_can_accept_and_becomes_a_member(): void
    {
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);
        Sanctum::actingAs($this->guest);

        $this->putJson("/api/channels/{$channel->id}/invitations/{$invitation->id}", ['status' => 'accepted'])
            ->assertOk();

        $this->assertSame('accepted', $invitation->fresh()->status);
        $this->assertTrue($channel->isMember($this->guest));
        $this->assertDatabaseHas('invitation_logs', ['invitation_id' => $invitation->id, 'action' => 'accepted']);
        Notification::assertSentTo($this->owner, AcceptedInvitationNotification::class);
    }

    public function test_the_invited_user_can_decline_and_is_then_invitable_again(): void
    {
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);
        Sanctum::actingAs($this->guest);

        $this->putJson("/api/channels/{$channel->id}/invitations/{$invitation->id}", ['status' => 'declined'])
            ->assertOk();
        $this->assertFalse($channel->isMember($this->guest));

        Sanctum::actingAs($this->owner);
        $this->postJson("/api/channels/{$channel->id}/invitations", ['identifier' => 'grace@example.com'])
            ->assertSuccessful();

        $this->assertSame(1, Invitation::count(), 're-inviting reuses the same invitation');
        $this->assertSame('pending', $invitation->fresh()->status);
    }

    public function test_someone_else_cannot_answer_an_invitation_for_another_user(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);
        Sanctum::actingAs(User::factory()->create());

        $this->putJson("/api/channels/{$channel->id}/invitations/{$invitation->id}", ['status' => 'accepted'])
            ->assertForbidden();

        $this->assertSame('pending', $invitation->fresh()->status);
        $this->assertSame(0, Member::count());
    }

    public function test_an_invitation_can_only_be_answered_once(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);
        $invitation->update(['status' => 'declined']);
        Sanctum::actingAs($this->guest);

        $this->putJson("/api/channels/{$channel->id}/invitations/{$invitation->id}", ['status' => 'accepted'])
            ->assertForbidden();
    }

    public function test_the_owner_can_cancel_but_the_invitee_cannot(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);

        Sanctum::actingAs($this->guest);
        $this->putJson("/api/channels/{$channel->id}/invitations/{$invitation->id}", ['status' => 'cancelled'])
            ->assertForbidden();

        Sanctum::actingAs($this->owner);
        $this->putJson("/api/channels/{$channel->id}/invitations/{$invitation->id}", ['status' => 'cancelled'])
            ->assertOk();
        $this->assertSame('cancelled', $invitation->fresh()->status);
    }

    public function test_the_signed_email_link_accepts_an_invitation_and_redirects_to_the_app(): void
    {
        Notification::fake();
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);

        $this->get(URL::signedRoute('invitations.accept', ['invitation' => $invitation->id]))
            ->assertRedirect('http://frontend.test/my-channels');

        $this->assertTrue($channel->isMember($this->guest));
    }

    public function test_the_signed_email_link_can_decline_an_invitation(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);

        $this->get(URL::signedRoute('invitations.deny', ['invitation' => $invitation->id]))
            ->assertRedirect('http://frontend.test/my-invitations');

        $this->assertSame('declined', $invitation->fresh()->status);
    }

    public function test_invitation_links_without_a_valid_signature_are_rejected(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $invitation = $this->pendingInvitation($channel, $this->guest);

        $this->get("/api/invitations/{$invitation->id}/accept")->assertForbidden();

        $this->assertSame('pending', $invitation->fresh()->status);
    }

    public function test_a_user_sees_only_their_own_pending_invitations(): void
    {
        $channel = $this->channelOwnedBy($this->owner);
        $mine = $this->pendingInvitation($channel, $this->guest);
        $this->pendingInvitation($channel, User::factory()->create());
        Sanctum::actingAs($this->guest);

        $ids = collect($this->getJson('/api/me/invitations')->assertOk()->json('data'))->pluck('id');

        $this->assertSame([$mine->id], $ids->all());
    }
}
