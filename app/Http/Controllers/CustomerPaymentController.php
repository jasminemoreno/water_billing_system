<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerPaymentController extends Controller
{
    // ---------------------------
    // SHOW BILLS FOR PAYMENT
    // ---------------------------
    public function index()
    {
        $customerId = auth('customer-api')->id();

        $bills = Bill::where('customer_id', $customerId)
            ->whereIn('status', ['Unpaid', 'Pending'])
            ->orderByDesc('billing_date')
            ->get();

        return response()->json($bills);
    }

    // ---------------------------
    // SUBMIT ONLINE PAYMENT
    // ---------------------------
    public function store(Request $request, $id)
{
    $customerId = auth('customer-api')->id();

    // Find the bill for this customer
    $bill = Bill::where('id', $id)
        ->where('customer_id', $customerId)
        ->first();

    if (!$bill) {
        return response()->json(['message' => 'Bill not found'], 404);
    }

    if ($bill->status === 'Paid') {
        return response()->json(['message' => 'This bill is already paid'], 400);
    }

    // Validate only the screenshot and optional message
    $validator = Validator::make($request->all(), [
        'screenshot' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'message' => 'nullable|string|max:255'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $filename = null;

    if ($request->hasFile('screenshot')) {
        $file = $request->file('screenshot');
        $filename = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('uploads/gcash'), $filename);
    }

    // Create payment using the bill's meter_no
    $payment = Payment::create([
        'customer_id' => $customerId,
        'bill_id' => $bill->id,
        'amount' => $bill->total,
        'meter_no' => $bill->meter_no, // ✅ get from bill, not customer
        'payment_method' => 'GCash',
        'gcash_screenshot' => $filename,
        'status' => 'Pending',
        'message' => $request->message // optional
    ]);

    // Update bill status to pending
    $bill->update(['status' => 'Pending']);

    // Create notification for the customer
    Notification::create([
        'customer_id' => $customerId,
        'payment_id' => $payment->id,
        'type' => 'payment',
        'message' => 'Your payment is submitted and pending approval.',
        'read' => false
    ]);

    return response()->json([
        'message' => 'Payment submitted successfully',
        'payment' => $payment
    ]);
}

    // ---------------------------
    // PAYMENT HISTORY
    // ---------------------------
    public function history()
    {
        $customerId = auth('customer-api')->id();

        $payments = Payment::with('bill')
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($payments);
    }
}