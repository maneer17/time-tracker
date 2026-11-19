<?php

namespace Tests\Feature;

use App\Enums\NotificationType;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\AcceptedInvitationNotification;
use App\Notifications\ChannelExportReadyNotification;
use App\Notifications\CommentDeletedByOwnerNotification;
use App\Notifications\NewCommentNotification;
use App\Notifications\NewInvitation;
use App\Notifications\NewSharedDayNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    private function notificationFor(User $user, array $attributes = []): DatabaseNotification
    {
        return $user->notifications()->create([
            'id'      => (string) Str::uuid(),
            'type'    => 'new_comments',
            'data'    => ['message' => 'Someone commented'],
            'read_at' => null,
            ...$attributes,
        ]);
    }

    public function test_a_user_lists_only_their_own_notifications_and_unread_count(): void
    {
        $me = User::factory()->create();
        $this->notificationFor($me);
        $this->notificationFor($me, ['read_at' => now()]);
        $this->notificationFor(User::factory()->create());
        Sanctum::actingAs($me);

        $this->getJson('/api/notifications')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/notifications/unread-count')->assertOk()->assertJsonPath('count', 1);
    }

    public function test_a_notification_can_be_marked_read_and_deleted_by_its_owner(): void
    {
        $me = User::factory()->create();
        $notification = $this->notificationFor($me);
        Sanctum::actingAs($me);

        $this->putJson("/api/notifications/{$notification->id}/read")->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);

        $this->deleteJson("/api/notifications/{$notification->id}")->assertOk();
        $this->assertModelMissing($notification);
    }

    public function test_a_user_cannot_touch_another_users_notification(): void
    {
        $notification = $this->notificationFor(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->putJson("/api/notifications/{$notification->id}/read")->assertForbidden();
        $this->deleteJson("/api/notifications/{$notification->id}")->assertForbidden();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_all_notifications_can_be_marked_read_at_once(): void
    {
        $me = User::factory()->create();
        $this->notificationFor($me);
        $this->notificationFor($me);
        Sanctum::actingAs($me);

        $this->putJson('/api/notifications/mark-all-as-read')->assertOk();

        $this->assertSame(0, $me->unreadNotifications()->count());
    }

    public function test_email_preferences_can_be_read_and_updated(): void
    {
        $me = User::factory()->create();
        Sanctum::actingAs($me);

        $this->getJson('/api/settings/notification-preferences')->assertOk()
            ->assertJsonCount(count(NotificationType::cases()), 'data');

        $this->putJson('/api/settings/notification-preferences', [
            'preferences' => [['notification_type' => 'new_comments', 'mail' => false]],
        ])->assertOk();

        $this->assertDatabaseHas('notifications_types', [
            'user_id' => $me->id, 'notification_type' => 'new_comments', 'mail' => false,
        ]);
        $this->assertDatabaseHas('notifications_types', [
            'user_id' => $me->id, 'notification_type' => 'new_shared_days', 'mail' => true,
        ]);
    }

    public function test_preferences_reject_unknown_notification_types(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/settings/notification-preferences', [
            'preferences' => [['notification_type' => 'made_up', 'mail' => true]],
        ])->assertStatus(422);
    }

    public function test_notification_payloads_carry_the_structured_fields_the_client_uses_to_localise_them(): void
    {
        $owner = User::factory()->create(['name' => 'Olivia Owner']);
        $guest = User::factory()->create(['name' => 'Grace Guest']);
        $channel = $this->channelOwnedBy($owner, ['name' => 'Design Team']);
        $sharedDay = $this->shareDay($channel, $owner, '2026-01-15');
        $comment = Comment::create(['shared_day_id' => $sharedDay->id, 'user_id' => $guest->id, 'body' => 'Looks great']);
        $comment->load(['author', 'sharedDay.channel']);
        $invitation = $this->pendingInvitation($channel, $guest);

        $payloads = [
            'invitation'    => [(new NewInvitation($invitation))->toArray($guest), ['from' => 'Olivia Owner', 'channel_name' => 'Design Team']],
            'accepted'      => [(new AcceptedInvitationNotification($invitation))->toArray($owner), ['from' => 'Grace Guest', 'channel_name' => 'Design Team']],
            'shared day'    => [(new NewSharedDayNotification($sharedDay))->toArray($guest), ['from' => 'Olivia Owner', 'channel_name' => 'Design Team']],
            'new comment'   => [(new NewCommentNotification($comment))->toArray($owner), ['from' => 'Grace Guest', 'channel_name' => 'Design Team', 'excerpt' => 'Looks great']],
            'comment gone'  => [(new CommentDeletedByOwnerNotification($comment))->toArray($guest), [
                'from' => 'Olivia Owner', 'channel_name' => 'Design Team', 'excerpt' => 'Looks great', 'shared_day_date' => '2026-01-15',
            ]],
            'export ready'  => [(new ChannelExportReadyNotification($channel, 'file.xlsx'))->toArray($owner), ['channel_name' => 'Design Team']],
        ];

        foreach ($payloads as $label => [$payload, $expected]) {
            $this->assertNotEmpty($payload['message'], "$label keeps its English message for old clients and emails");
            foreach ($expected as $key => $value) {
                $this->assertSame($value, $payload[$key], "$label: $key");
            }
        }
    }

    /** One of each notification type, paired with the user it is sent to. */
    private function everyNotificationType(): array
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $channel = $this->channelOwnedBy($owner);
        $sharedDay = $this->shareDay($channel, $owner);
        $comment = Comment::create(['shared_day_id' => $sharedDay->id, 'user_id' => $guest->id, 'body' => 'Looks great']);
        $comment->load(['author', 'sharedDay.channel']);
        $invitation = $this->pendingInvitation($channel, $guest);

        return [
            NotificationType::ChannelInvitations->value    => [new NewInvitation($invitation), $guest],
            NotificationType::AcceptedInvitations->value   => [new AcceptedInvitationNotification($invitation), $owner],
            NotificationType::NewSharedDays->value         => [new NewSharedDayNotification($sharedDay), $guest],
            NotificationType::NewComments->value           => [new NewCommentNotification($comment), $owner],
            NotificationType::CommentDeletedByOwner->value => [new CommentDeletedByOwnerNotification($comment), $guest],
            NotificationType::ChannelExportReady->value    => [new ChannelExportReadyNotification($channel, 'file.xlsx'), $owner],
        ];
    }

    public function test_every_notification_type_sends_email_when_the_user_wants_it(): void
    {
        foreach ($this->everyNotificationType() as $type => [$notification, $user]) {
            $this->assertContains('mail', $notification->via($user), "$type is emailed when the setting is on");

            $user->notificationTypes()->where('notification_type', $type)->update(['mail' => false]);
            $this->assertNotContains('mail', $notification->via($user), "$type is not emailed when the setting is off");
        }
    }

    public function test_live_notifications_have_the_same_type_and_shape_as_saved_ones(): void
    {
        foreach ($this->everyNotificationType() as $type => [$notification, $user]) {
            $notification->id = (string) Str::uuid();

            $live = (new BroadcastNotificationCreated($user, $notification, $notification->toBroadcast($user)->data))->broadcastWith();

            $this->assertSame($type, $live['type'], "$type: the live type matches the stored one, not the class name");
            $this->assertSame($type, $notification->databaseType($user));
            $this->assertSame($notification->id, $live['id']);
            $this->assertArrayHasKey('data', $live, "$type: the live payload carries its data");
            $this->assertSame($notification->toArray($user), $live['data']);
        }
    }

    public function test_email_is_only_added_when_the_user_wants_it_for_that_type(): void
    {
        $owner = User::factory()->create();
        $channel = $this->channelOwnedBy($owner);
        $comment = Comment::create([
            'shared_day_id' => $this->shareDay($channel, $owner)->id, 'user_id' => $owner->id, 'body' => 'hi',
        ]);
        $notification = new NewCommentNotification($comment);

        $this->assertSame(['database', 'broadcast', 'mail'], $notification->via($owner));

        $owner->notificationTypes()->where('notification_type', 'new_comments')->update(['mail' => false]);
        $this->assertSame(['database', 'broadcast'], $notification->via($owner));
    }

    public function test_the_unsubscribe_link_turns_off_email_for_that_type(): void
    {
        $user = User::factory()->create();
        $url = URL::signedRoute('email.unsubscribe', ['user' => $user->id, 'type' => 'new_comments']);

        $this->get($url)->assertRedirect('http://frontend.test/unsubscribed?type=new_comments');

        $this->assertDatabaseHas('notifications_types', [
            'user_id' => $user->id, 'notification_type' => 'new_comments', 'mail' => false,
        ]);
    }

    public function test_the_unsubscribe_link_cannot_be_forged(): void
    {
        $user = User::factory()->create();

        $this->get("/api/email/unsubscribe?user={$user->id}&type=new_comments")->assertForbidden();

        $this->assertDatabaseHas('notifications_types', [
            'user_id' => $user->id, 'notification_type' => 'new_comments', 'mail' => true,
        ]);
    }

    public function test_the_prune_command_removes_only_old_read_notifications(): void
    {
        $user = User::factory()->create();
        $oldRead = $this->notificationFor($user, ['read_at' => now()->subDays(40), 'created_at' => now()->subDays(40)]);
        $oldUnread = $this->notificationFor($user, ['created_at' => now()->subDays(40)]);
        $recentRead = $this->notificationFor($user, ['read_at' => now(), 'created_at' => now()->subDays(2)]);

        $this->artisan('notifications:prune', ['--days' => 30])->assertSuccessful();

        $this->assertModelMissing($oldRead);
        $this->assertModelExists($oldUnread);
        $this->assertModelExists($recentRead);
    }
}
