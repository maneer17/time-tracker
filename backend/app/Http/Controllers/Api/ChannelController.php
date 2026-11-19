<?php

namespace App\Http\Controllers\Api;
use App\Models\Channel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ChannelResource;
use App\Http\Requests\{StoreChannelRequest, UpdateChannelRequest};
class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type'); // owned | joined

        $query = match ($type) {
            'owned'  => Channel::owned(auth()->user()),
            'joined' => Channel::joined(auth()->user()),
            default  => Channel::accessibleTo(auth()->user()),
        };

        $channels = $query
            ->withCount('members')
            ->latest()
            ->paginate($this->paginate);

        return ChannelResource::collection($channels);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChannelRequest $request)
    {
        $channel = auth()->user()->channels()->create($request->validated());
        return new ChannelResource($channel);
    }

    /**
     * Display the specified resource.
     */
    public function show(Channel $channel)
    {
        $this->authorize('view', $channel);

        $channel->load('members.user')->loadCount('members');

        return new ChannelResource($channel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $this->authorize('update', $channel);

        $validated = $request->validated();
        $channel->update($validated);
        return new ChannelResource($channel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Channel $channel)
    {
        $this->authorize('delete', $channel);

        $channel->delete();
        return new ChannelResource($channel);
    }
}
