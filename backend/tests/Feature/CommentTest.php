<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use App\Notifications\CommentDeletedByOwnerNotification;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\BuildsTrackerData;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase, BuildsTrackerData;

    private User $owner;
    private User $member;
    private User $otherMember;
    private $channel;
    private $sharedDay;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->member = User::factory()->create();
        $this->otherMember = User::factory()->create();
        $this->channel = $this->channelOwnedBy($this->owner);
        $this->addMember($this->channel, $this->member);
        $this->addMember($this->channel, $this->otherMember);
        $this->sharedDay = $this->shareDay($this->channel, $this->owner);
    }

    private function comment(User $author, string $body = 'Nice work'): Comment
    {
        return Comment::create(['shared_day_id' => $this->sharedDay->id, 'user_id' => $author->id, 'body' => $body]);
    }

    private function url(?Comment $comment = null): string
    {
        return "/api/shared-days/{$this->sharedDay->id}/comments".($comment ? "/{$comment->id}" : '');
    }

    public function test_a_member_can_comment_and_the_channel_owner_is_notified(): void
    {
        Notification::fake();
        Sanctum::actingAs($this->member);

        $response = $this->postJson($this->url(), ['body' => 'Great progress!'])
            ->assertCreated()->assertJsonPath('data.body', 'Great progress!');

        // ISO 8601, so each client can format it in the user's language
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $response->json('data.created_at'));

        $this->assertDatabaseHas('comments', ['user_id' => $this->member->id, 'body' => 'Great progress!']);
        Notification::assertSentTo($this->owner, NewCommentNotification::class);
    }

    public function test_outsiders_cannot_read_or_write_comments(): void
    {
        $this->comment($this->member);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson($this->url())->assertForbidden();
        $this->postJson($this->url(), ['body' => 'Let me in'])->assertForbidden();
    }

    public function test_members_can_read_comments(): void
    {
        $this->comment($this->member);
        Sanctum::actingAs($this->otherMember);

        $this->getJson($this->url())->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_a_comment_needs_a_body_of_reasonable_length(): void
    {
        Sanctum::actingAs($this->member);

        $this->postJson($this->url(), ['body' => ''])->assertStatus(422)->assertJsonValidationErrors('body');
        $this->postJson($this->url(), ['body' => str_repeat('a', 1001)])->assertStatus(422);
    }

    public function test_only_the_author_can_edit_a_comment(): void
    {
        $comment = $this->comment($this->member, 'Original');

        foreach ([$this->otherMember, $this->owner] as $notAuthor) {
            Sanctum::actingAs($notAuthor);
            $this->putJson($this->url($comment), ['body' => 'Tampered'])->assertForbidden();
        }
        $this->assertSame('Original', $comment->fresh()->body);

        Sanctum::actingAs($this->member);
        $this->putJson($this->url($comment), ['body' => 'Edited'])->assertOk();
        $this->assertSame('Edited', $comment->fresh()->body);
    }

    public function test_an_author_can_delete_their_own_comment_without_notifying_anyone(): void
    {
        Notification::fake();
        $comment = $this->comment($this->member);
        Sanctum::actingAs($this->member);

        $this->deleteJson($this->url($comment))->assertOk();

        $this->assertModelMissing($comment);
        Notification::assertNothingSent();
    }

    public function test_the_channel_owner_can_moderate_and_the_author_is_told(): void
    {
        Notification::fake();
        $comment = $this->comment($this->member);
        Sanctum::actingAs($this->owner);

        $this->deleteJson($this->url($comment))->assertOk();

        $this->assertModelMissing($comment);
        Notification::assertSentTo($this->member, CommentDeletedByOwnerNotification::class);
    }

    public function test_another_member_cannot_delete_someone_elses_comment(): void
    {
        $comment = $this->comment($this->member);
        Sanctum::actingAs($this->otherMember);

        $this->deleteJson($this->url($comment))->assertForbidden();

        $this->assertModelExists($comment);
    }

    public function test_a_channel_owner_cannot_moderate_comments_from_another_channel(): void
    {
        $otherOwner = User::factory()->create();
        $otherChannel = $this->channelOwnedBy($otherOwner);
        $otherDay = $this->shareDay($otherChannel, $otherOwner);
        $victim = $this->comment($this->member); // lives on $this->sharedDay in *this* channel
        Sanctum::actingAs($otherOwner);

        // $otherOwner passes their own shared day id, but targets a comment from a different channel.
        $this->deleteJson("/api/shared-days/{$otherDay->id}/comments/{$victim->id}")->assertNotFound();
        $this->putJson("/api/shared-days/{$otherDay->id}/comments/{$victim->id}", ['body' => 'x'])->assertNotFound();

        $this->assertModelExists($victim);
    }
}
