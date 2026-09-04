<?php

namespace App\Policies;

use App\Models\{Organization, User};
use App\Enums\OrganizationRole;

class OrganizationPolicy
{
    public function view(User $user, Organization $organization): bool
    {
        return $this->membership($user, $organization) !== null;
    }

    public function update(User $user, Organization $organization): bool
    {
        $role = $this->membership($user, $organization)?->pivot->role;

        return in_array($role, [OrganizationRole::Owner->value, OrganizationRole::Admin->value]);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->membership($user, $organization)?->pivot->role === OrganizationRole::Owner->value;
    }

    public function transferOwnership(User $user, Organization $organization, User $newUser): bool
    {
        return $this->membership($user, $organization)?->pivot->role === OrganizationRole::Owner->value
            && in_array(
                $this->membership($newUser, $organization)?->pivot->role,
                [OrganizationRole::Member->value, OrganizationRole::Admin->value]
            );
    }



    private function membership(User $user, Organization $organization)
    {
        return $user->orgs()
            ->wherePivot('organization_id', $organization->id)
            ->first();
    }
}