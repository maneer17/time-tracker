<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private const MIN_SEARCH_LENGTH = 2;
    private const MAX_RESULTS = 10;

    /**
     * Member search for the invite dialog. Deliberately narrow: it needs a search term,
     * returns a handful of matches, and exposes only what is needed to pick a person
     * (never the Google id or anything else on the account).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        if (mb_strlen($search) < self::MIN_SEARCH_LENGTH) {
            return UserResource::collection(collect());
        }

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->whereKeyNot($request->user()->id)
            ->where(fn ($query) => $query
                ->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%"))
            ->orderBy('name')
            ->limit(self::MAX_RESULTS)
            ->get();

        return UserResource::collection($users);
    }
}
