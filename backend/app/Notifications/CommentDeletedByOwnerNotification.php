<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
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
        $this->fromName      = $comment->sharedDay->channel->owner->name; // the owner who deleted it
        $this->channelId     = $comment->sharedDay->channel->id;
        $this->channelName   = $comment->sharedDay->channel->name;
        $this->sharedDayDate = $comment->sharedDay->date->format('Y-m-d');
        $this->commentBody   = $comment->body;
    }

    public function via(object $notifiable): array
    {
        return $this->viaEmail($notifiable, NotificationType::CommentDeletedByOwner)
            ? ['database', 'broadcast', 'mail']
            : ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $unsubscribeUrl = URL::signedRoute('email.unsubscribe', [
            'user' => $notifiable->id,
            'type' => NotificationType::CommentDeletedByOwner->value,
        ]);

        return (new MailMessage)
            ->subject('Your comment was removed')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your comment "' . Str::limit($this->commentBody, 100) . '" on the shared day ' . $this->sharedDayDate . ' in ' . $this->channelName . ' was removed by the channel owner.')
            ->action('View Channel', config('app.frontend_url') . '/channels/' . $this->channelId)
            ->line('[Unsubscribe from these emails](' . $unsubscribeUrl . ')')
            ->salutation('Regards, ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->commentId,
            'from'       => $this->fromName,
            'channel_id' => $this->channelId,
            'channel_name'    => $this->channelName,
            'shared_day_date' => $this->sharedDayDate,
            'excerpt'         => Str::limit($this->commentBody, 50),
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

    // Laravel's broadcast channel would otherwise send the class name as `type`; keep it equal to the stored value
    // so live and saved notifications look identical to the client.
    public function broadcastType(): string
    {
        return NotificationType::CommentDeletedByOwner->value;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id'         => $this->id,
            'type'       => $this->databaseType($notifiable),
            'data'       => $this->toArray($notifiable),
            'read_at'    => null,
            'created_at' => now()->toISOString(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('CommentDeletedByOwner notification failed', [
            'comment_id' => $this->commentId,
            'error'      => $exception->getMessage(),
        ]);
    }
}