<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\{Channel, Invitation, User};
use App\Events\InvitationSent;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreInvitationRequest, UpdateInvitationRequest};
use App\Http\Resources\InvitationResource;
use App\Services\{InvitationStatusService, InviteService};

class InvitationController extends Controller
{
    public function index(Channel $channel)
    {
        $this->authorize('viewAny', [Invitation::class, $channel]);
        $invitations = Invitation::query()
            ->forChannel($channel)
            ->pending()
            ->with(['invitedUser', 'channel'])
            ->latest()
            ->paginate($this->paginate);


        return InvitationResource::collection($invitations);
    }



    public function store(StoreInvitationRequest $request, Channel $channel, InviteService $service )
    {
        $this->authorize('create', [Invitation::class, $channel]);
        $validated = $request->validated();
        $identifier = $validated['identifier'];
        $invitation = $service->invite($identifier, $channel);
        event(new InvitationSent($invitation)); 
        return new InvitationResource($invitation);
    }

    public function update(UpdateInvitationRequest $request, Channel $channel, Invitation $invitation, InvitationStatusService $service)
    {
        $validated = $request->validated();
        $status = $validated['status'];

        match ($status) {
            'accepted'  => $this->authorize('accept', $invitation),
            'declined'  => $this->authorize('decline', $invitation),
            'cancelled' => $this->authorize('cancel', $invitation),
        };

        $service->changeStatus($invitation, $status, auth()->user());
        return new InvitationResource($invitation);
    }

    public function accept(Request $request, Invitation $invitation, InvitationStatusService $service)
        {
            try {
                $service->accept($invitation, $invitation->invitedUser);
                return redirect(config('app.frontend_url') . '/my-channels');
            } catch (DomainException $e) {
                return redirect(config('app.frontend_url') . '/my-invitations?error=' . urlencode($e->getMessage()));
            }
        }

        public function deny(Request $request, Invitation $invitation, InvitationStatusService $service)
        {
            try {
                $service->decline($invitation, $invitation->invitedUser);
                return redirect(config('app.frontend_url') . '/my-invitations');
            } catch (DomainException $e) {
                return redirect(config('app.frontend_url') . '/my-invitations?error=' . urlencode($e->getMessage()));
            }
        }

}
