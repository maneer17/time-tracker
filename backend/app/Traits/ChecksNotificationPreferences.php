<?php

namespace App\Traits;

use App\Enums\NotificationType;

trait ChecksNotificationPreferences
{
    private function viaEmail(object $notifiable, NotificationType $type): bool
    {
        return $notifiable->notificationTypes()
            ->where('notification_type', $type)
            ->where('mail', true)
            ->exists();
    }
}