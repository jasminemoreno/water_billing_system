<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Bill;

class CustomerAuthController extends Controller
{
    // -------------------------
    // Customer Login
    // -------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or inactive account.'
            ], 401);
        }

        // Create Sanctum token
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'customer' => $customer
        ]);
    }

    // -------------------------
    // Customer Dashboard
    // -------------------------
    public function dashboard(Request $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $currentBill = Bill::where('meter_no', $customer->meter_no)
            ->latest('created_at')
            ->first();

        $lastPayment = null;

        if ($currentBill) {
            $lastPayment = $currentBill->payments()
                ->latest('created_at')
                ->first();
        }

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'currentBill' => $currentBill,
            'lastPayment' => $lastPayment
        ]);
    }

    // -------------------------
    // Customer Logout
    // -------------------------
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}