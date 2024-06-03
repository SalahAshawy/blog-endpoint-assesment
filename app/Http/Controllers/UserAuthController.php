<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $registerUserData = $request->validate([
                'name' => 'required|string|min:5',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|min:8',
                'number' => 'required|min:11|max:11'
            ]);
            
            $user = User::create([
                'name' => $registerUserData['name'],
                'email' => $registerUserData['email'],
                'password' => Hash::make($registerUserData['password']),
                'number' => $registerUserData['number'],
            ]);
            
            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            // Store the token in a cookie
            Cookie::queue('auth_token', $token, 60 * 24); // Expires in 24 hours

            return response()->json([
                'message' => 'User Created',
                'user' => $user,
                'access_token' => $token,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
  
    public function login(Request $request)
    {
        try {
            $loginUserData = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|min:8'
            ]);

            $user = User::where('email', $loginUserData['email'])->first();

            if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
                return response()->json(['message' => 'Invalid Credentials'], 401);
            }

            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

            // Store the token in a cookie
            Cookie::queue('auth_token', $token, 60 * 24); // Expires in 24 hours

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'user' => $user,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        // Remove the auth token cookie
        Cookie::queue(Cookie::forget('auth_token'));

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
