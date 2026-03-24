<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreTimeEntryRequest, UpdateTimeEntryRequest};
use App\Http\Resources\TimeEntryResource;
use App\Models\TimeEntry;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['date', 'sort', 'search', 'history']);

        if ($request->boolean('history')) {
            $dates = auth()->user()
                ->time_entries()
                ->history()
                ->paginate(15);

            return response()->json([
                'data' => $dates->pluck('date'),
                'current_page' => $dates->currentPage(),
                'last_page' => $dates->lastPage(),
                'per_page' => $dates->perPage(),
                'total' => $dates->total(),
                'from' => $dates->firstItem(),
                'to' => $dates->lastItem(),
            ]);
        }

        $time_entries = auth()->user()
            ->time_entries()
            ->search($filters)
            ->paginate(10);

        return TimeEntryResource::collection($time_entries);
    }


    public function store(StoreTimeEntryRequest $request)
    {
        $validated = $request->validated();
        $time_entry = auth()->user()->time_entries()->create($validated);
        return new TimeEntryResource($time_entry);
    }

    public function show(TimeEntry $time_entry)
    {
        return new TimeEntryResource($time_entry);
    }

    public function update(UpdateTimeEntryRequest $request, TimeEntry $time_entry)
    {
        $validated = $request->validated();
        $time_entry->update($validated);
        return new TimeEntryResource($time_entry);
    }

    public function destroy(TimeEntry $time_entry)
    {
        $time_entry->delete();
        return new TimeEntryResource($time_entry);
    }
}
