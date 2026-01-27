<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    // Register Method
    public function register(RegisterUserRequest $request)
    {

        $validated = $request->validated();

        // Create New User

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'role' => 'customer'

        ]);

        // Create Token For New User
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'user created successfully',
            'user' => $user,
            'token' => $token,
            'token-type' => 'Bearer'
        ], 201);
    }

    // Login User 
    public function login(LoginUserRequest $request)
    {

        $validated = $request->validated();

        if (!Auth::attempt($validated))
        {
            return response()->json([
                'message' => 'Invalid Credintials (AuthController Line 71)'
            ]);
        }

        // Get The User Token 
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;
        
        if ($validated['remember'] ?? false) {
            $token = $user->createToken('auth_token', ['remember'])->plainTextToken;
        }
        return response()->json([
            'message' => 'Logged In Successfully',
            'user' => $user,
            'token' => $token,
            'token-type' => 'Bearer'
        ]);
    }

    // Logout User
    public function logout(Request $request)
    {   
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'logged out successfully',
        ]);
    }
}
