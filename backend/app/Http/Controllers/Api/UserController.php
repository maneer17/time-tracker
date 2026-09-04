<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\App;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $org    = App::make('active_org'); // set by the org middleware

        // Scope to THIS org's members, then search within them.
        $users = $org->users()
            ->when($search, fn ($query) =>
                $query->where(fn ($q) =>
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                )
            )
            ->get();

        return UserResource::collection($users);
    }
}