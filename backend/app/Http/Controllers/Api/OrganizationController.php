<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Organization, User};
use App\Http\Resources\OrganizationResource;
use App\Http\Requests\{StoreOrgRequest, UpdateOrgRequest};
use App\Enums\OrganizationRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    public function index()
    {
        return OrganizationResource::collection(Auth::user()->orgs);
    }

    public function store(StoreOrgRequest $request)
    {
        $org = Organization::create($request->validated());

        Auth::user()->orgs()->attach($org->id, [
            'role' => OrganizationRole::Owner->value,
        ]);

        return new OrganizationResource($org);
    }

    public function show(Organization $organization)
    {
        $this->authorize('view', $organization);

        return new OrganizationResource($organization);
    }

    public function update(UpdateOrgRequest $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $organization->update($request->validated());

        return new OrganizationResource($organization);
    }

    public function destroy(Organization $organization)
    {
        $this->authorize('delete', $organization);

        $organization->delete();

        return response()->json(['message' => 'Organization deleted.']);
    }

    public function leave(Organization $org)
    {
        $this->authorize('view', $org);

        $org  = Auth::user()->orgs()->wherePivot('organization_id', $org->id)->first();
        $role = $org->pivot->role;

        if ($role === OrganizationRole::Owner->value) {
            return response()->json([
                'message' => 'You are the owner. Transfer ownership or delete the organization before leaving.',
            ], 409);
        }

        Auth::user()->orgs()->wherePivot('organization_id', $organization->id)->detach();

        return response()->json(['message' => 'Left the organization.']);
    }

    public function transferOwnership(Request $request, Organization $org)
    {
        $newOwner = User::findOrFail(
            $request->validate(['new_owner_id' => 'required|exists:users,id'])['new_owner_id']
        );

        $this->authorize('transferOwnership', [$org, $newOwner]);

        Auth::user()->orgs()->updateExistingPivot($org->id, [
            'role' => OrganizationRole::Admin->value,
        ]);

        $newOwner->orgs()->updateExistingPivot($org->id, [
            'role' => OrganizationRole::Owner->value,
        ]);

        return response()->json(['message' => 'Ownership transferred.']);
    }
}