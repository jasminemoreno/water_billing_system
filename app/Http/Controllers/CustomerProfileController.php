<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerProfileController extends Controller
{
    // Fetch authenticated customer profile
    public function show(Request $request)
    {
        $customer = $request->user('customer-api'); // Use API guard

        return response()->json([
            'success' => true,
            'customer' => [
                'id'            => $customer->id,
                'customer_name' => $customer->customer_name,
                'phone'         => $customer->phone,
                'gender'        => $customer->gender,
                'birth'         => $customer->birth,
                'avatar_url'    => $customer->avatar
                    ? asset('storage/profile_photos/' . $customer->avatar)
                    : asset('assets/img/icons/profile.png'),
            ]
        ]);
    }

    // Update customer profile
    public function update(Request $request)
    {
        $customer = $request->user('customer-api'); // Use authenticated customer

        // Validate inputs
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'nullable|in:Male,Female',
            'birth'         => 'nullable|date',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Update fields
        $customer->customer_name = $request->customer_name;
        $customer->phone         = $request->phone;
        $customer->gender        = $request->gender;
        $customer->birth         = $request->birth;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file     = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Store in public disk under profile_photos
            $file->storeAs('profile_photos', $filename, 'public');

            // Delete old avatar if exists
            if ($customer->avatar && Storage::disk('public')->exists('profile_photos/' . $customer->avatar)) {
                Storage::disk('public')->delete('profile_photos/' . $customer->avatar);
            }

            $customer->avatar = $filename;
        }

        $customer->save();

        // Return JSON for frontend
        return response()->json([
            'success' => true,
            'customer' => [
                'id'            => $customer->id,
                'customer_name' => $customer->customer_name,
                'phone'         => $customer->phone,
                'gender'        => $customer->gender,
                'birth'         => $customer->birth,
                'avatar_url'    => $customer->avatar
                    ? asset('storage/profile_photos/' . $customer->avatar)
                    : asset('assets/img/icons/profile.png'),
            ]
        ]);
    }
}