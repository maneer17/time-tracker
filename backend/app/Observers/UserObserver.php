<?php

namespace App\Observers;
use App\Models\User;
use App\Models\NotificationsTypes;
use App\Enums\NotificationType;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Enums\OrganizationRole;
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

    // Personal org — every user gets one, same pattern as the backfill
    $org = Organization::create([
        'name' => $user->name . "'s Organization",
    ]);

    OrganizationUser::create([
        'user_id'         => $user->id,
        'organization_id' => $org->id,
        'role'            => OrganizationRole::Owner->value,
    ]);

    Mail::to($user->email)->queue(new WelcomeMail($user));
}


}
