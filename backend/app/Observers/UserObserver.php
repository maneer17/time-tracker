<?php

namespace App\Observers;
use App\Models\User;
use App\Models\NotificationsTypes;
use App\Enums\NotificationType;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
class UserObserver
{
    public function created(User $user)
{
    foreach (NotificationType::cases() as $type) {
        NotificationsTypes::create([
            'user_id' => $user->id,
            'mail' => true,
            'notification_type' => $type->value
        ]);
    }

    Mail::to($user->email)->queue(new WelcomeMail($user));
}


}
