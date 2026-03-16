<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    // Return admin data as JSON (API)
    public function show(Request $request)
    {
        $admin = $request->user('admin-api'); // use API guard

        return response()->json([
            'id' => $admin->id,
            'fullname' => $admin->fullname,
            'phone' => $admin->phone,
            'gender' => $admin->gender,
            'birthdate' => $admin->birthdate,
            'profile_photo' => $admin->profile_photo,
        ]);
    }

    // Update only phone (and optional other profile fields)
    public function updatePhone(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'phone' => 'nullable|string|max:20',
        ]);

        $admin->phone = $request->phone;
        $admin->save();

        return response()->json([
            'message' => 'Phone number updated successfully.',
            'admin' => $admin,
        ]);
    }

    // Optional: full profile update (for future use)
    public function updateProfile(Request $request)
    {
        $admin = $request->user('admin-api');

        $request->validate([
            'fullname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female',
            'birthdate' => 'nullable|date',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $filename = time().'_'.$photo->getClientOriginalName();
            $photo->storeAs('profile_photos', $filename, 'public');

            // Delete old photo
            if ($admin->profile_photo && Storage::disk('public')->exists('profile_photos/'.$admin->profile_photo)) {
                Storage::disk('public')->delete('profile_photos/'.$admin->profile_photo);
            }

            $admin->profile_photo = $filename;
        }

        $admin->fullname = $request->fullname ?? $admin->fullname;
        $admin->phone = $request->phone ?? $admin->phone;
        $admin->gender = $request->gender ?? $admin->gender;
        $admin->birthdate = $request->birthdate ?? $admin->birthdate;

        $admin->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'admin' => $admin,
        ]);
    }
}