<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;


class TimeEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      $start = Carbon::parse($this->start_time);
      $end = Carbon::parse($this->end_time);
        $diff = $start->diff($end);
        $timeTaken = [
              "hours"=> $diff->h,
              "minutes" => $diff->i,
              "seconds" => $diff->s
        ];
        
              return [
            'id' => $this->id,
            'label' => $this->label,
            'start_time' => Carbon::parse($this->start_time)->format('g:i a'),
            'end_time' => Carbon::parse($this->end_time)->format('g:i a'),
            'time_taken' => $timeTaken,
            
              ];
    
}
}