<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Enums\NotificationType;
use Illuminate\Support\Str;

class CommentDeletedByOwnerNotification extends Notification implements ShouldQueue
{
    use Queueable, ChecksNotificationPreferences;

    public $tries   = 3;
    public $backoff = [30, 60, 120];
    // we store (the job will also serialize) plain primitives here cuz the comment will be deleted 
    // storing primitives avoids any DB lookup when the queue worker picks this up  
    // https://dev.to/alchermd/laravel-queued-notifications-for-a-deleted-user-or-eloquent-model-36fl
    private int    $commentId;
    private string $fromName;
    private int    $channelId;
    private string $channelName;
    private string $sharedDayDate;
    private string $commentBody;

    public function __construct(Comment $comment)
    {
        // extract everything now, while comment still exists in DB
        // the listener is synchronous so this constructor runs before $comment->delete()
        $this->commentId     = $comment->id;
        $this->fromName      = $comment->author->name;
        $this->channelId     = $comment->sharedDay->channel->id;
        $this->channelName   = $comment->sharedDay->channel->name;
        $this->sharedDayDate = $comment->sharedDay->date->format('Y-m-d');
        $this->commentBody   = $comment->body;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->commentId,
            'from'       => $this->fromName,
            'channel_id' => $this->channelId,
            'message'    => 'Your comment: ' . Str::limit($this->commentBody, 50) .
                            ' on shared day ' . $this->sharedDayDate .
                            ' for the channel ' . $this->channelName .
                            ' has been deleted by the channel owner',
            'type'       => NotificationType::CommentDeletedByOwner->value,
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationType::CommentDeletedByOwner->value;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // reuse toArray — single source of truth, no duplication
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('CommentDeletedByOwner notification failed', [
            'comment_id' => $this->commentId,
            'error'      => $exception->getMessage(),
        ]);
    }
}