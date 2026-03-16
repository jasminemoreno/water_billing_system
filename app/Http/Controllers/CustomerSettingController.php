<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerSettingController extends Controller
{

    // =========================
    // LOAD CUSTOMER SETTINGS
    // =========================
    public function index()
    {
        $customer = auth()->guard('customer-api')->user();

        return response()->json([
            'success' => true,
            'customer' => [
                'phone' => $customer->phone,
                'email' => $customer->email
            ]
        ]);
    }


    // =========================
    // CHANGE PASSWORD
    // =========================
    public function changePassword(Request $request)
    {
        try {

            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);

        }

        $customer = auth()->guard('customer-api')->user();

        // check current password
        if (!Hash::check($request->current_password, $customer->password)) {

            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);

        }

        // update password
        $customer->password = Hash::make($request->new_password);
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }


    // =========================
    // UPDATE ACCOUNT INFO
    // =========================
    public function updateAccount(Request $request)
    {

        try {

            $request->validate([
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:customers,email,' . auth()->guard('customer-api')->id(),
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 422);

        }

        $customer = auth()->guard('customer-api')->user();

        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully.'
        ]);
    }

}