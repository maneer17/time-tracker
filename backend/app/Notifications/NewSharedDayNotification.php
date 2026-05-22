<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\SharedDay;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class NewSharedDayNotification extends Notification implements ShouldQueue
{
    use Queueable, ChecksNotificationPreferences;

    public $tries   = 3;
    public $backoff = [30, 60, 120];

    public function __construct(private SharedDay $sharedDay)
    {
        $this->sharedDay->loadMissing(['channel.owner']);
    }

    public function via(object $notifiable): array
    {
        return $this->viaEmail($notifiable, NotificationType::NewSharedDays)
            ? ['database', 'broadcast', 'mail']
            : ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'shared_day_id' => $this->sharedDay->id,
            'channel_id' => $this->sharedDay->channel->id,
            'from'          => $this->sharedDay->channel->owner->name,
            'message'       => $this->sharedDay->channel->owner->name . ' shared a new day in ' . $this->sharedDay->channel->name,
            'type'          => NotificationType::NewSharedDays->value,
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationType::NewSharedDays->value;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'shared_day_id' => $this->sharedDay->id,
            'channel_id' => $this->sharedDay->channel->id,
            'from'          => $this->sharedDay->channel->owner->name,
            'message'       => $this->sharedDay->channel->owner->name . ' shared a new day in ' . $this->sharedDay->channel->name,
            'type'          => NotificationType::NewSharedDays->value,
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url') . '/channels/' . $this->sharedDay->channel_id;
        $unsubscribeUrl = URL::signedRoute('email.unsubscribe', [
            'user' => $notifiable->id,
            'type' => NotificationType::NewSharedDays->value,
        ]);

        return (new MailMessage)
            ->subject('A new day has been shared in ' . $this->sharedDay->channel->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->sharedDay->channel->owner->name . ' has shared a new day in ' . $this->sharedDay->channel->name)
            ->line('Date: ' . Carbon::parse($this->sharedDay->date)->format('M d, Y'))
            ->line('Number of entries: ' . $this->sharedDay->entries_count)
            ->action('View Day', $url)
            ->line('[Unsubscribe from these emails](' . $unsubscribeUrl . ')')
            ->salutation('Regards, ' . config('app.name'));
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('NewSharedDayNotification failed', [
            'shared_day_id' => $this->sharedDay->id,
            'error'         => $exception->getMessage(),
        ]);
    }
}