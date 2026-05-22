<?php

namespace Database\Seeders;

use App\Enums\NotificationType;
use App\Models\NotificationsTypes;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationPreferencesSeeder extends Seeder
{
    public function run(): void
    {
        User::each(function (User $user) {
            foreach (NotificationType::cases() as $type) {
                NotificationsTypes::firstOrCreate(
                    [
                        'user_id'           => $user->id,
                        'notification_type' => $type->value,
                    ],
                    [
                        'mail' => true,
                    ]
                );
            }
        });
    }
}