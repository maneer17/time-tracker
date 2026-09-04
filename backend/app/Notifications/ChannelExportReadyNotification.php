<?php

namespace App\Notifications;

use App\Models\Channel;
use App\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ChannelExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Channel $channel,
        private string $filename
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
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
            'message'      => 'Your export for ' . $this->channel->name . ' is ready.',
            'download_url' => $this->downloadUrl(),
            'type'         => NotificationType::ChannelExportReady->value,
        ];
    }

    public function databaseType(object $notifiable): string
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