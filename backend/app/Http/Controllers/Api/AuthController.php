<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            "name" =>"required",
            "email"=>"required|email",
            "password"=>"required"
        ]);
        $user = User::create($validated);
         $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'token'=> $token
        ]);
    }
    public function login(Request $request){
        $request->validate([
            "email"=>"required|email",
            "password"=>"required"
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user){
            throw ValidationException::withMessages([
                "email"=> ["incorrect credentials"],
            ]);
        }
        if(!Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages(
                [
                    "password"=> ["incorrect credentials"]
                ]
                );
        }
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'token'=> $token
        ]);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(["message"=>"logged out successfully"]);
    }
}
