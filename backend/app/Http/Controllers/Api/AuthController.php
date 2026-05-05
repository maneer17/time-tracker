<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\{StoreUserRequest, GoogleRequest}; 
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Socialite;


class AuthController extends Controller
{
    public function store(StoreUserRequest $request)
{
    $validated = $request->validated();

    $user = User::create($validated);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user
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
            'token'=> $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(["message" => "Logged out successfully " . $user->name]);
    }





    public function google(GoogleRequest $request)
    {
        $token = $request->validated()['token'];

        try {
            // userFromToken() works with access tokens 
            // take access token from frontend and verify it 
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($token);

        } catch (\Exception $e) {
        return response()->json([
            'message' => 'Invalid Google token',
        ], 401);
    }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'              => $googleUser->getName(),
                'google_id'         => $googleUser->getId(),
                'password'          => null,
                'email_verified_at' => now(),
            ]
        );

        if (!$user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
            ]);
            // google_id here in case the user changed their email but not their entire account 
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

}
