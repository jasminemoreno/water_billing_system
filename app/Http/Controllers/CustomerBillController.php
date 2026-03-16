<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;

class CustomerBillController extends Controller
{
    /**
     * Display bills assigned to the authenticated customer
     */
    public function index(Request $request)
    {
        // Get the authenticated customer
        $customer = Auth::guard('customer-api')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Retrieve bills only for this customer
        $bills = Bill::where('customer_id', $customer->id)
                     ->orderBy('billing_date', 'desc')
                     ->get();

        return response()->json($bills);
    }
}