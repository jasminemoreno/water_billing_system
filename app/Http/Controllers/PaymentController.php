<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Notification;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // List all payments with optional search (exclude soft-deleted)
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $payments = Payment::with(['customer', 'bill'])
            ->whereNull('deleted_at') // only active payments
            ->when($search, function ($query, $search) {
                $query->whereHas('customer', fn($q) => $q->where('customer_name', 'like', "%$search%"))
                      ->orWhereHas('bill', fn($q) => $q->where('meter_no', 'like', "%$search%"))
                      ->orWhere('id', 'like', "%$search%");
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json($payments);
    }

    // Search customers for unpaid bills
    public function searchCustomer(Request $request)
    {
        $query = $request->get('query', '');
        if (!$query) return response()->json([]);

        $bills = Bill::with('customer')
            ->where('status', 'Unpaid')
            ->whereHas('customer', fn($q) => $q->where('customer_name', 'like', "%$query%"))
            ->get()
            ->map(fn($bill) => [
                'bill_id' => $bill->id,
                'customer_id' => $bill->customer->id,
                'customer_name' => $bill->customer->customer_name,
                'meter_no' => $bill->meter_no,
                'amount' => $bill->total,
                'paid' => $bill->status === 'Paid'
            ]);

        return response()->json($bills);
    }

    // Add new payment
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'bill_id' => 'required|exists:bills,id',
            'amount' => 'required|numeric|min:0',
            'penalty' => 'nullable|numeric|min:0',
        ]);

        $bill = Bill::findOrFail($request->bill_id);

        if ($bill->status === 'Paid') {
            return response()->json(['success' => false, 'message' => 'This bill has already been paid.'], 400);
        }

        $penalty = $request->penalty ?? 0;
        $totalAmount = $request->amount + $penalty;

        $payment = Payment::create([
            'customer_id' => $request->customer_id,
            'bill_id' => $request->bill_id,
            'meter_no' => $bill->meter_no,
            'amount' => $totalAmount,
            'payment_method' => 'Cash',
            'status' => 'Verified',
            'penalty' => $penalty,
        ]);

        $bill->update(['status' => 'Paid']);

        Notification::create([
            'customer_id' => $request->customer_id,
            'payment_id' => $payment->id,
            'type' => 'payment',
            'message' => 'Your payment of ₱' . $totalAmount . ' has been approved.',
            'read' => false
        ]);

        return response()->json(['success' => true, 'message' => 'Payment added successfully', 'payment' => $payment]);
    }

    // Delete payment (soft delete)
    public function destroy(int $id)
    {
        $payment = Payment::findOrFail($id);

        // Soft delete regardless of status (optional: still prevent if you want)
        $payment->delete();

        return response()->json(['success' => true, 'message' => 'Payment deleted successfully']);
    }
}