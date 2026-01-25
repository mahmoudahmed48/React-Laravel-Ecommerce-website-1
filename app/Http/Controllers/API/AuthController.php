<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register Method
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'Errors' => $validator->errors()
            ], 422);
        }

        // Create New User

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()->json([
                'message' => 'Invalid Credintials (AuthController Line 71)'
            ]);
        }

        // Get The User Token 
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;

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
