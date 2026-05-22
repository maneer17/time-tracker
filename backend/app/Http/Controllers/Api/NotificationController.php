<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use Illuminate\Notifications\DatabaseNotification;
class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(10);

        
        return NotificationResource::collection($notifications);
    }

    public function unreadCount()
    {
        $count = auth()->user()
            ->unreadNotifications()
            ->count();

        return response()->json(['count' => $count]);
    }


    public function markAsRead(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->id()) {
        abort(403);
    }
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(){
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroy(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== auth()->id()) {
        abort(403);
        }
        $notification->delete();
        return response()->json(['message' => 'Notification deleted']);
    }
}