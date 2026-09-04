<?php

namespace App\Http\Controllers\Api\Settings;
use App\Http\Resources\NotificationPreferencesResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationsSettingsRequest;
use Illuminate\Http\Request;
use App\Models\{User,NotificationsTypes};
use App\Enums\NotificationType;
class NotificationPreferencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return NotificationPreferencesResource::collection(
            $request->user()->notificationTypes
        );
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationsSettingsRequest $request)
    {
        foreach ($request->validated()['preferences'] as $preference) {
            $request->user()->notificationTypes()
                ->where('notification_type', $preference['notification_type'])
                ->update(['mail' => $preference['mail']]);
        }

        return response()->json(['message' => 'Preferences updated successfully']);
    }

    public function unsubscribe(Request $request)
        {
            $user = User::findOrFail($request->query('user'));
            $type = NotificationType::from($request->query('type'));

            $user->notificationTypes()
                ->where('notification_type', $type)
                ->update(['mail' => false]);

            return redirect(config('app.frontend_url') . '/unsubscribed?type=' . $type->value);
        }

    /**
     * Remove the specified resource from storage.
     */
}
