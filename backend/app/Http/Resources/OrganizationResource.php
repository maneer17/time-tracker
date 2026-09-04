<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'settings'    => $this->settings,
            // present only when loaded via the pivot (e.g. "my orgs" listing)
            'role'        => $this->whenPivotLoaded('organization_user', fn () => $this->pivot->role),
            'created_at'  => $this->created_at,
        ];
    }
}
