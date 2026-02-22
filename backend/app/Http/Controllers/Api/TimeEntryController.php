<?php

namespace App\Http\Controllers\Api; 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeEntryRequest; 
use App\Http\Requests\UpdateTimeEntryRequest; 
use Illuminate\Http\Request;
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
                ->pluck('date');

            return response()->json($dates);
        }

        $time_entries = auth()->user()
            ->time_entries()
            ->search($filters)
            ->paginate(15);

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