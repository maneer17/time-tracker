<?php

namespace App\Http\Controllers\Api;

use App\Models\{Channel, SharedDay};
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSharedDayRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SharedDayResource;

class SharedDayController extends Controller
{
    // GET /channels/{channel}/shared-days
    public function index(Channel $channel)
    {
        $this->authorize('viewAny', $channel);

        return SharedDayResource::collection(
            $channel->sharedDays()->get()
        );
    }

    // GET /channels/{channel}/shared-days/{shared_day}
    public function show(Channel $channel, SharedDay $sharedDay)
    {
        $this->authorize('view', $channel);

        return new SharedDayResource($sharedDay->load(['entries.timeEntry', 'channel']));
    }

    // POST /shared-days
    public function store(StoreSharedDayRequest $request)
    {
        $sharedDays = collect();

        DB::transaction(function () use ($request, &$sharedDays) {
            foreach ($request->channel_ids as $channelId) {
                $channel = Channel::findOrFail($channelId);
                $this->authorize('create', [SharedDay::class, $channel]);

                $sharedDay = SharedDay::firstOrCreate([
                    'channel_id' => $channelId,
                    'user_id'    => auth()->id(),
                    'date'       => $request->date,
                ]);

                foreach ($request->entry_ids as $entryId) {
                    $sharedDay->entries()->firstOrCreate([
                        'time_entry_id' => $entryId
                    ]);
                }

                $sharedDay->load(['entries.timeEntry', 'channel']);
                $sharedDays->push($sharedDay);
            }
        });

        return SharedDayResource::collection($sharedDays);
    }

    // DELETE /channels/{channel}/shared-days/{shared_day}
    public function destroy(Channel $channel, SharedDay $sharedDay)
    {
        $this->authorize('delete', $sharedDay);

        $sharedDay->delete();
        return response()->json(['message' => 'Shared day removed successfully.']);
    }
}