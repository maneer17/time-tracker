<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TimeEntryResource;
use App\Models\TimeEntry;
use App\Http\Requests\StoreTimeEntryRequest; 
use Illuminate\Support\Carbon;

class TimeEntryController extends Controller
{
    public function index()
    { 
        $time_entries = auth()->user()->time_entries()->whereDate('created_at', today())->get(); 
        return TimeEntryResource::collection($time_entries);
    }
    
    public function byDate(string $date)
    {
        $time_entries = auth()->user()->time_entries()->date($date)->get();
        return TimeEntryResource::collection($time_entries);
    }

    public function store(StoreTimeEntryRequest $request)
    {
        $validated = $request->validated();
        $time_entry = auth()->user()->time_entries()->create($validated);
        return new TimeEntryResource($time_entry);
    }

    public function history()
    {
        $dates = auth()->user()->time_entries()
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');
        
        return $dates;
    }

    public function show(TimeEntry $time_entry)
    {
        return new TimeEntryResource($time_entry);
    }

    public function update(Request $request, TimeEntry $time_entry)
    {
        
        $validated = $request->validate([
            "end_time" => "|date_format:H:i|after:start_time"
        ]);
        
        $time_entry->update($validated);
        return new TimeEntryResource($time_entry);
    }

    public function destroy(TimeEntry $time_entry)
    {
        

        
        $time_entry->delete();
        return response()->json([], 204);
    }
}