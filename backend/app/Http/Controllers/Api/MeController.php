<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Http\Resources\{InvitationResource, SharedDayResource};

class MeController extends Controller
{
    public function invitations(Request $request)
    {
        $invitations = Invitation::query()
            ->received($request->user())
            ->pending()
            ->with(['invitedBy', 'channel'])
            ->latest()
            ->paginate();

        return InvitationResource::collection($invitations);
    }

   public function sharedDays()
    {
        $sharedDays = auth()->user()
            ->sharedDays()
            ->with('channel')
            ->get()
            ->groupBy('date');

        return $sharedDays->map(function ($days, $date) {
            return [
                'date'     => $date,
                'channels' => $days->map(fn($day) => [
                'id'         => $day->channel->id,
                'name'       => $day->channel->name,
                'shared_day_id' => $day->id,
                ])
            ];
        })->values();
    }
}