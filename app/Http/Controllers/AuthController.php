<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * SIGNUP (API)
     */
    public function signup(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = Admin::create([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully',
            'admin' => $admin,
            'token' => $token
        ], 201);
    }

    /**
     * LOGIN (API)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $admin->tokens()->delete();
        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    /**
     * LOGOUT (API)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * GET AUTHENTICATED USER
     */
    public function user(Request $request)
    {
        return response()->json([
            'admin' => $request->user()
        ]);
    }

    /**
     * UPDATE EMAIL AND PASSWORD (API)
     */
    public function updateEmail(Request $request)
    {
        $admin = $request->user(); // authenticated admin
    
        $request->validate([
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ]);
    
        $admin->email = $request->email;
        $admin->save();
    
        return response()->json([
            'message' => 'Email updated successfully',
            'admin' => $admin
        ]);
    }

    public function updatePassword(Request $request)
{
    $admin = $request->user('sanctum'); // make sure you're using sanctum guard
    
    if (!$admin) {
        return response()->json(['error' => 'Unauthorized.'], 401);
    }

    // Validate input
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:6|confirmed', // expects new_password_confirmation
    ]);

    // Check current password
    if (!\Hash::check($request->current_password, $admin->password)) {
        return response()->json(['error' => 'Current password is incorrect.'], 422);
    }

    // Update password
    $admin->password = \Hash::make($request->new_password);
    $admin->save();

    return response()->json(['message' => 'Password updated successfully.']);
}
}