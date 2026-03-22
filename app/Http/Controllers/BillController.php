<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Notification;
use Carbon\Carbon;

class BillController extends Controller
{
    /**
     * Get all bills (JSON) for Vue table
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $bills = Bill::with('customer')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('customer_name', 'like', "%$search%");
                })->orWhere('meter_no', 'like', "%$search%");
            })
            ->orderBy('billing_date', 'desc')
            ->get();

        return response()->json($bills);
    }

    /**
     * AJAX: search customers for autocomplete
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->get('query', '');
        $customers = Customer::where('customer_name', 'like', "%$query%")
            ->orWhere('meter_no', 'like', "%$query%")
            ->get(['id', 'customer_name', 'meter_no']);

        return response()->json($customers);
    }

    /**
     * Store a new bill
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_no' => 'required|string',
            'consumption' => 'required|numeric|min:0',
            'billing_date' => 'required|date',
        ]);

        // Normalize billing_date to only YYYY-MM-DD
        $billingDate = Carbon::parse($validated['billing_date'])->startOfDay();
        $billingMonth = $billingDate->month;
        $billingYear = $billingDate->year;

        // Prevent duplicate bill for the same customer/month/year
        $existingBill = Bill::where('customer_id', $validated['customer_id'])
            ->whereMonth('billing_date', $billingMonth)
            ->whereYear('billing_date', $billingYear)
            ->first();

        if ($existingBill) {
            return response()->json([
                'success' => false,
                'message' => 'This customer already has a bill for this month.',
                'errors' => ['billing_date' => 'Duplicate bill']
            ], 422);
        }

        // Auto-calculate due and disconnection dates
        $dueDate = $billingDate->copy()->addDays(7);      // 7 days after billing
        $disconnectionDate = $dueDate->copy()->addDays(5); // 5 days after due

        $bill = Bill::create([
            'customer_id' => $validated['customer_id'],
            'meter_no' => $validated['meter_no'],
            'consumption' => $validated['consumption'],
            'total' => $validated['consumption'] * 10,
            'billing_date' => $billingDate,
            'due_date' => $dueDate,
            'disconnection_date' => $disconnectionDate,
            'status' => 'Unpaid', // ✅ FIXED: set Unpaid by default, not Pending
        ]);

        // Create notification
        Notification::create([
            'customer_id' => $bill->customer_id,
            'type' => 'bill_created',
            'message' => "Your bill for meter {$bill->meter_no} is ready. Due date: {$dueDate->format('M d, Y')}, Disconnection date: {$disconnectionDate->format('M d, Y')}.",
            'read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bill added successfully!',
            'bill' => $bill->load('customer')
        ]);
    }

    /**
     * Update an existing bill
     */
    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_no' => 'required|string',
            'consumption' => 'required|numeric|min:0',
            'billing_date' => 'required|date',
        ]);

        $billingDate = Carbon::parse($validated['billing_date'])->startOfDay();
        $billingMonth = $billingDate->month;
        $billingYear = $billingDate->year;

        // Prevent duplicate bill for same customer/month/year excluding this bill
        $existingBill = Bill::where('customer_id', $validated['customer_id'])
            ->whereMonth('billing_date', $billingMonth)
            ->whereYear('billing_date', $billingYear)
            ->where('id', '!=', $bill->id)
            ->first();

        if ($existingBill) {
            return response()->json([
                'success' => false,
                'message' => 'This customer already has a bill for this month.',
                'errors' => ['billing_date' => 'Duplicate bill']
            ], 422);
        }

        // Auto-calculate due and disconnection dates
        $dueDate = $billingDate->copy()->addDays(7);
        $disconnectionDate = $dueDate->copy()->addDays(5);

        $bill->update([
            'customer_id' => $validated['customer_id'],
            'meter_no' => $validated['meter_no'],
            'consumption' => $validated['consumption'],
            'total' => $validated['consumption'] * 10,
            'billing_date' => $billingDate,
            'due_date' => $dueDate,
            'disconnection_date' => $disconnectionDate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bill updated successfully!',
            'bill' => $bill->load('customer')
        ]);
    }

    /**
     * Soft delete a bill
     */
    public function destroy(Bill $bill)
    {
        $bill->delete();
        return response()->json(['success' => true, 'message' => 'Bill deleted successfully.']);
    }
}