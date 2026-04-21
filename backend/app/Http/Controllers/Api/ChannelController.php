<?php

namespace App\Http\Controllers\Api;
use App\Models\Channel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ChannelResource;
use App\Http\Requests\{StoreChannelRequest, UpdateChannelRequest};
class ChannelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Channel::class, 'channel');
    }




    public function index()
    {
        $owned = Channel::owned(auth()->user())->latest()->get();
        $joined = Channel::joined(auth()->user())->latest()->get();
            return response()->json([
             'owned' => ChannelResource::collection($owned),
            'joined' => ChannelResource::collection($joined),
    ]);
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
        return new ChannelResource($channel->load(['members.user', 'invitations']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $validated = $request->validated();
        $channel->update($validated);
        return new ChannelResource($channel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Channel $channel)
    {
        $channel->delete();
        return new ChannelResource($channel);
    }
}
