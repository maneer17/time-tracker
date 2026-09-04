<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SharedDayResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'date'          => $this->date->format('Y-m-d'),
            'channel_id'    => $this->channel_id,
            'total_time'    => $this->total_time,
            'entries_count' => $this->entries_count,
            'entries'       => SharedDayEntryResource::collection($this->whenLoaded('entries')),
            'channel'       => new ChannelResource($this->whenLoaded('channel')),
        ];
    }
}