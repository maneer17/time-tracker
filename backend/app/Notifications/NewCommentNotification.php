<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Enums\NotificationType;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable, ChecksNotificationPreferences;

    public $tries   = 3;
    public $backoff = [30, 60, 120];

    public function __construct(private Comment $comment)
    {
        $this->comment->loadMissing(['author', 'sharedDay.channel']);
    }

    public function via(object $notifiable): array
    {
        return $this->viaEmail($notifiable, NotificationType::NewComments)
            ? ['database', 'broadcast', 'mail']
            : ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'from'  => $this->comment->author->name,
            'message'    => $this->comment->author->name . ' commented on your shared day: ' . Str::limit($this->comment->body, 50),
            'type'       => NotificationType::NewComments->value,
            'channel_id' => $this->comment->sharedDay->channel->id,
            'shared_day_id' => $this->comment->sharedDay->id
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationType::NewComments->value;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'comment_id' => $this->comment->id,
            'from'       => $this->comment->author->name,
            'message'    => $this->comment->author->name . ' commented on your shared day: ' . Str::limit($this->comment->body, 50),
            'type'       => NotificationType::NewComments->value,
            'channel_id' => $this->comment->sharedDay->channel->id,
            'shared_day_id' => $this->comment->sharedDay->id
            
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url') . '/channels/' . $this->comment->sharedDay->channel->id . '/shared-days/' . $this->comment->sharedDay->id;
        $unsubscribeUrl = URL::signedRoute('email.unsubscribe', [
            'user' => $notifiable->id,
            'type' => NotificationType::NewComments->value,
        ]);

        return (new MailMessage)
            ->subject('New comment on your shared day')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->comment->author->name . ' commented on your shared day:')
            ->line(Str::limit($this->comment->body, 100))
            ->action('View Comment', $url)
            ->line('[Unsubscribe from these emails](' . $unsubscribeUrl . ')')
            ->salutation('Regards, ' . config('app.name'));
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('NewCommentNotification failed', [
            'comment_id' => $this->comment->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}