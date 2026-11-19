<?php

namespace App\Http\Controllers\Api;

use App\Events\NewSharedDayEvent;
use App\Models\{Channel, SharedDay};
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSharedDayRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SharedDayResource;

class SharedDayController extends Controller
{
    // GET /channels/{channel}/shared-days
    public function index(Channel $channel)
    {
        $this->authorize('view', $channel);

        return SharedDayResource::collection(
            $channel->sharedDays()->paginate($this->paginate)
        );
    }

    // GET /channels/{channel}/shared-days/{shared_day}
    public function show(Channel $channel, SharedDay $sharedDay)
    {
        $this->authorize('view', $channel);

        return new SharedDayResource($sharedDay->load(['entries.timeEntry', 'channel', 'comments.author']));
    }

    // POST /shared-days
    public function store(StoreSharedDayRequest $request)
    {
        $sharedDays = collect();
        // The frontend sends the day as an ISO timestamp; MySQL's date column needs Y-m-d.
        $date = Carbon::parse($request->date)->toDateString();

        DB::transaction(function () use ($request, $date, &$sharedDays) {
            foreach ($request->channel_ids as $channelId) {
                $channel = Channel::findOrFail($channelId);
                $this->authorize('create', [SharedDay::class, $channel]);

                $sharedDay = SharedDay::firstOrCreate([
                    'channel_id' => $channelId,
                    'user_id'    => auth()->id(),
                    'date'       => $date,
                ]);

                foreach ($request->entry_ids as $entryId) {
                    $sharedDay->entries()->firstOrCreate([
                        'time_entry_id' => $entryId
                    ]);
                }

                $sharedDay->load(['entries.timeEntry', 'channel']);
                event(new NewSharedDayEvent($sharedDay));
                $sharedDays->push($sharedDay);
            }
        });

        return SharedDayResource::collection($sharedDays);
    }

    public function destroy(Channel $channel, SharedDay $sharedDay)
    {
        $this->authorize('delete', $sharedDay);

        $sharedDay->delete();
        return new SharedDayResource($sharedDay);
    }
}
