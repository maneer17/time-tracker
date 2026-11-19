<?php

namespace App\Notifications;

use App\Models\Channel;
use App\Enums\NotificationType;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ChannelExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable, ChecksNotificationPreferences;

    public function __construct(
        private Channel $channel,
        private string $filename
    ) {}

    public function via(object $notifiable): array
    {
        return $this->viaEmail($notifiable, NotificationType::ChannelExportReady)
            ? ['database', 'broadcast', 'mail']
            : ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $unsubscribeUrl = URL::signedRoute('email.unsubscribe', [
            'user' => $notifiable->id,
            'type' => NotificationType::ChannelExportReady->value,
        ]);

        return (new MailMessage)
            ->subject('Your channel export is ready')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your export for ' . $this->channel->name . ' is ready. The download link works for 24 hours.')
            ->action('Download export', $this->downloadUrl())
            ->line('[Unsubscribe from these emails](' . $unsubscribeUrl . ')')
            ->salutation('Regards, ' . config('app.name'));
    }

    private function downloadUrl(): string
    {
        return URL::temporarySignedRoute(
            'exports.download',
            now()->addHours(24),
            [
                'user'     => $this->channel->owner->id,
                'filename' => $this->filename,
            ]
        );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'channel_id'   => $this->channel->id,
            'channel_name' => $this->channel->name,
            'message'      => 'Your export for ' . $this->channel->name . ' is ready.',
            'download_url' => $this->downloadUrl(),
            'type'         => NotificationType::ChannelExportReady->value,
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationType::ChannelExportReady->value;
    }

    // Laravel's broadcast channel would otherwise send the class name as `type`; keep it equal to the stored value
    // so live and saved notifications look identical to the client.
    public function broadcastType(): string
    {
        return NotificationType::ChannelExportReady->value;
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
}