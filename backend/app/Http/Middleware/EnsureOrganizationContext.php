<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $orgId = $request->header('X-Organization-Id');

        if (! $orgId) {
            return response()->json(['message' => 'Organization context required.'], 400);
        }
        // Checked first, so we never reveal whether an org exists to a non-member.
        $isMember = $request->user()
            ->orgs()
            ->wherePivot('organization_id', $orgId)
            ->exists();

        if (! $isMember) {
            return response()->json(['message' => 'You do not belong to this organization.'], 403);
        }

        $org = Organization::find($orgId);
        App::instance('active_org', $org);

        return $next($request);
    }
}