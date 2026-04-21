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
              return [
            'id' => $this->id,
            'label' => $this->label,
            'start_time' => $this->start_time->format('g:i a'),
            'end_time' => $this->end_time->format('g:i a'),
            'time_taken' => $this->time_taken,
            'created_at' => $this->created_at
            
              ];
    
}
}