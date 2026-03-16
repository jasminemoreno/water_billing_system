<?php

namespace App\Http\Controllers;
use App\Models\Payment;

use Illuminate\Http\Request;

class CustomerHistoryController extends Controller
{
   // CustomerPaymentController.php
public function history()
{
    $customerId = auth('customer-api')->id();

    $payments = Payment::with('bill')
        ->where('customer_id', $customerId)
        ->where('status', 'Verified') // only successful payments
        ->orderByDesc('created_at')
        ->get();

    return response()->json([
        'success' => true,
        'payments' => $payments
    ]);
}
}
