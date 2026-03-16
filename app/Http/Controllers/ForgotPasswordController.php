<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return response()->json(['error' => 'Email not found.'], 404);
        }

        $otp = rand(100000, 999999);

        PasswordOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(5)]
        );

        // Send OTP via email
        try {
            Mail::raw("Your OTP is: {$otp}", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Your Password Reset OTP');
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send OTP email.'], 500);
        }

        return response()->json(['success' => 'OTP sent successfully']);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['error' => 'Invalid or expired OTP.'], 422);
        }

        return response()->json(['success' => 'OTP verified']);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return response()->json(['error' => 'Admin not found.'], 404);
        }

        $admin->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete OTP record
        PasswordOtp::where('email', $request->email)->delete();

        return response()->json(['success' => 'Password updated successfully']);
    }
}