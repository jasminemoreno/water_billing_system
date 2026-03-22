<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class CustomerHistoryController extends Controller
{
    public function history()
    {
        // Get logged customer
        $customerId = auth('customer-api')->id();

        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get only VERIFIED (successful) payments
        $payments = Payment::with(['bill' => function ($q) {
                $q->withTrashed(); // ⭐ include soft deleted bills
            }])
            ->where('customer_id', $customerId)
            ->where('status', 'Verified') // ⭐ only successful payments
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $payments->count(),
            'payments' => $payments
        ]);
    }
}