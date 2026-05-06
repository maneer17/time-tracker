<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
class UserController extends Controller
{
    public function index(Request $request)
    {
        $identifier = $request->input("search");
        $query = User::query();
        $users = $query->searchByNameOrEmail($identifier)->get();
        return UserResource::collection($users);

    }


}
