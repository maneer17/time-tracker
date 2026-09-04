<?php

namespace App\Notifications;

use App\Models\Invitation;
use App\Enums\NotificationType;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AcceptedInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable, ChecksNotificationPreferences;

    public $tries   = 3;
    public $backoff = [30, 60, 120];

    public function __construct(private Invitation $invitation)
    {
        $this->invitation->loadMissing(['invitedUser', 'channel']);
    }

    public function via(object $notifiable): array
    {
        return $this->viaEmail($notifiable, NotificationType::AcceptedInvitations)
            ? ['database', 'broadcast']
            : ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'channel_id' => $this->invitation->channel->id,
            'from'          => $this->invitation->invitedUser->name,
            'message'       => $this->invitation->invitedUser->name . ' accepted your invitation to ' . $this->invitation->channel->name,
            'type'          => NotificationType::AcceptedInvitations->value,
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationType::AcceptedInvitations->value;
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

    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url') . '/channels';
        $unsubscribeUrl = URL::signedRoute('email.unsubscribe', [
            'user' => $notifiable->id,
            'type' => NotificationType::AcceptedInvitations->value,
        ]);

        return (new MailMessage)
            ->subject('Someone accepted your invitation')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->invitation->invitedUser->name . ' has accepted your invitation to ' . $this->invitation->channel->name)
            ->action('View Channel', $url)
            ->line('[Unsubscribe from these emails](' . $unsubscribeUrl . ')')
            ->salutation('Regards, ' . config('app.name'));
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('AcceptedInvitationNotification failed', [
            'invitation_id' => $this->invitation->id,
            'error'         => $exception->getMessage(),
        ]);
    }
}