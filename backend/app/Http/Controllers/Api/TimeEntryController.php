<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TimeEntryResource ;
use App\Models\TimeEntry;
use Middleware\Authenticate;
use Illuminate\Support\Carbon;
class TimeEntryController extends Controller
{
    public function __construct(){
        $this->middleware('auth:sanctum');
    }
    public function index()
    { 
        $time_entries = auth()->user()->time_entries()->whereDate('created_at', today())->get();
        return TimeEntryResource::collection($time_entries);

    }
    public function byDate(string $date){
       $time_entries = auth()->user()
        ->time_entries()        
        ->date($date)   
        ->get();
        return TimeEntryResource::collection($time_entries);
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        "label"      => "required",
        "start_time" => "required|date_format:H:i",
        "end_time"   => "required|date_format:H:i|after:start_time"
    ]);

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

    return $dates ;
}


    /**
     * Display the specified resource.
     */
    public function show(TimeEntry $time_entry)
    {
        return new TimeEntryResource($time_entry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeEntry $time_entry)
    {
        $time_entry->update(
            [ $request->validate( [
            "end_time"=>"sometimes|after:start_time"])
            ]
            );
            return new TimeEntryResource($time_entry);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeEntry $time_entry)
    {
        $time_entry->delete();
        return response()->json(status: 204);
    }
}
