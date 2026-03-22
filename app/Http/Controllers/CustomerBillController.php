<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;

class CustomerBillController extends Controller
{

    // ===============================
    // PAYBILL PAGE → ONLY UNPAID / PENDING
    // ===============================
    
    // ===============================
    // MYBILL PAGE → ALL BILLS
    // ===============================
    public function myBills()
    {
        $customer = Auth::guard('customer-api')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $bills = Bill::withTrashed() // ⭐ VERY IMPORTANT
        ->where('customer_id',$customer->id)
        ->orderByDesc('id')
        ->get();

        return response()->json($bills);
    }

}