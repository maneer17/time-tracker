<?php

namespace App\Notifications;

use App\Models\Invitation;
use App\Traits\ChecksNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Enums\NotificationType;
use Illuminate\Support\Facades\URL;

class NewInvitation extends Notification implements ShouldQueue
{
    use Queueable, ChecksNotificationPreferences;

    public $tries   = 3;
    public $backoff = [30, 60, 120];

    public function __construct(private Invitation $invitation)
    {
        // loadMissing only queries if not already loaded — no double queries
        $this->invitation->loadMissing(['invitedBy', 'channel']);
    }

    public function via(object $notifiable): array
    {
        // check if user has email enabled for this notification type
        return $this->viaEmail($notifiable, NotificationType::ChannelInvitations)
            ? ['database', 'broadcast', 'mail']
            : ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        // this is what gets stored in the notifications table (database channel)
        // frontend uses this data to display the notification in the bell dropdown
        // we send IDs only — frontend is responsible for building the route
        return [
            'invitation_id' => $this->invitation->id,
            'from'          => $this->invitation->invitedBy->name,
            'channel_id' => $this->invitation->channel->id,
            'message'       => $this->invitation->invitedBy->name . ' invited you to ' . $this->invitation->channel->name,
            'type'          => NotificationType::ChannelInvitations->value,
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return NotificationType::ChannelInvitations->value;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        // this is what gets pushed to the frontend in real-time via Reverb
        // should match toArray() so the frontend receives consistent data
        // whether the notification comes from DB on page load or real-time via Echo
        return new BroadcastMessage([
            'invitation_id' => $this->invitation->id,
            'channel_id' => $this->invitation->channel->id,
            'from'          => $this->invitation->invitedBy->name,
            'message'       => $this->invitation->invitedBy->name . ' invited you to ' . $this->invitation->channel->name,
            'type'          => NotificationType::ChannelInvitations->value,
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        // signed routes are used here because the user clicking from email is NOT authenticated
        // the signature proves the URL hasn't been tampered with — no auth needed
        // signedRoute (no expiry) is fine for accept/deny — links should work anytime
        $acceptUrl = URL::signedRoute('invitations.accept', [
            'invitation' => $this->invitation->id,
        ]);

        $denyUrl = URL::signedRoute('invitations.deny', [
            'invitation' => $this->invitation->id,
        ]);

        // unsubscribe also uses signed URL — user is not authenticated
        // we pass user id and notification type so backend knows what to disable
        $unsubscribeUrl = URL::signedRoute('email.unsubscribe', [
            'user' => $notifiable->id,
            'type' => NotificationType::ChannelInvitations->value,
        ]);

        return (new MailMessage)
            ->subject('You have a new channel invitation')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->invitation->invitedBy->name . ' has invited you to their channel ' . $this->invitation->channel->name)
            ->action('Accept Invitation', $acceptUrl)
            ->line('[Unsubscribe from these emails](' . $unsubscribeUrl . ')')
            ->salutation('Regards, ' . config('app.name'));
    }

    public function failed(\Throwable $exception): void
    {
        // fires when the notification fails after all retries ($tries = 3)
        // logs the error silently — never bubbles up to the user
        \Log::error('NewInvitation notification failed', [
            'invitation_id' => $this->invitation->id,
            'error'         => $exception->getMessage(),
        ]);
    }
}