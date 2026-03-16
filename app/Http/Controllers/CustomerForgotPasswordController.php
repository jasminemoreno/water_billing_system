<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerForgotPasswordController extends Controller
{
    /**
     * Send OTP to customer's email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        $otp = rand(100000, 999999);
        cache()->put('customer_otp_'.$customer->email, $otp, now()->addMinutes(10));

        Mail::raw("Your OTP code is: $otp", function ($message) use ($customer) {
            $message->to($customer->email)
                    ->subject('Your OTP for Password Reset');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email'
        ]);
    }

    /**
     * Verify OTP entered by customer
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp'   => 'required|digits:6',
        ]);

        $cachedOtp = cache()->get('customer_otp_'.$request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        // OTP is valid
        return response()->json([
            'success' => true,
            'message' => 'OTP verified'
        ]);
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|string|min:6|confirmed', // password_confirmation expected
        ]);

        $customer = Customer::where('email', $request->email)->first();
        $customer->password = Hash::make($request->password);
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}