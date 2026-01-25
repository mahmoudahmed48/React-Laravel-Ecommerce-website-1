<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * Get User Profile.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('orders')
        ]);
    }


    /**
     * Update Profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'current_password' => 'required_with:mew_password',
            'new_password' => 'sometimes|required|min:8|confirmed'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'name', 'email', 'phone', 'address', 'city', 'country', 'postal_code'
        ]));

        if($request->has('new_password'))
        {
            if (!Hash::check($request->current_password, $user->password))
            {
                return response()->json([
                    'message' => 'current password is incorrect!'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        return response()->json([
            'message' => 'Profile Updated Successfully!'
        ]);
    }
}
