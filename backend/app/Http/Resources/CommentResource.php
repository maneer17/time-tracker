<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'body'       => $this->body,
            'created_at' => $this->created_at->format('M d, Y h:i A'),
            "shared_day_id" => $this->shared_day_id,
            'author'     => new UserResource($this->whenLoaded('author')),
            'can_edit'   => auth()->id() === $this->user_id,
            'can_delete' => auth()->id() === $this->user_id ||
                            $this->whenLoaded('sharedDay', fn() =>
                                $this->sharedDay->channel->isOwner(auth()->user())
                            , false)
        ];
    }
}
