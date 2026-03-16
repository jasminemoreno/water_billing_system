<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Customer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get all payments for the payment table.
     * This is used for the main admin payment table search.
     * Can search by customer name, meter no, or payment ID.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $payments = Payment::with(['customer', 'bill'])
            ->when($search, function ($query, $search) {
                $query->whereHas('customer', fn($q) => $q->where('customer_name', 'like', "%$search%"))
                      ->orWhereHas('bill', fn($q) => $q->where('meter_no', 'like', "%$search%"))
                      ->orWhere('id', 'like', "%$search%");
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json($payments);
    }

    /**
     * Search customers for Add Payment popup.
     * Only returns bills that are unpaid.
     */
    public function searchCustomer(Request $request)
    {
        $query = $request->get('query', '');

        if (!$query) {
            return response()->json([]);
        }

        $bills = Bill::with('customer')
            ->where('status', 'Unpaid') // Only unpaid bills
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

    /**
     * Store manual payment (cash/onsite) by admin.
     */
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
            return response()->json([
                'success' => false,
                'message' => 'This bill has already been paid.'
            ], 400);
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

        // Mark the bill as paid
        $bill->update(['status' => 'Paid']);

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully',
            'payment' => $payment,
        ]);
    }

    /**
     * Update payment (edit) by admin.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Pending,Verified,Rejected'
        ]);

        $payment = Payment::findOrFail($id);
        $payment->amount = $request->amount;
        $payment->status = $request->status;
        $payment->save();

        // Update bill status if needed
        $bill = $payment->bill;
        if ($payment->status === 'Verified') {
            $bill->update(['status' => 'Paid']);
        } elseif ($payment->status === 'Rejected') {
            $bill->update(['status' => 'Unpaid']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'payment' => $payment
        ]);
    }

    /**
     * Update payment status only (for online payments verification)
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Verified,Rejected'
        ]);

        $payment = Payment::with('bill')->findOrFail($id);
        $payment->status = $request->status;
        $payment->save();

        if ($payment->status === 'Verified') {
            $payment->bill->update(['status' => 'Paid']);
        } elseif ($payment->status === 'Rejected') {
            $payment->bill->update(['status' => 'Unpaid']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'payment' => $payment
        ]);
    }

    /**
     * Delete payment
     */
    public function destroy(int $id)
    {
        $payment = Payment::findOrFail($id);
        $bill = $payment->bill;

        // If payment was verified, revert bill status
        if ($payment->status === 'Verified') {
            $bill->update(['status' => 'Unpaid']);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}