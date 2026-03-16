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
     * Get all bills (JSON) for Vue
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
        // -------------------------
        // 1. Validate input
        // -------------------------
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meter_no' => 'required|string',
            'consumption' => 'required|numeric|min:0',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'disconnection_date' => 'required|date|after_or_equal:due_date',
        ]);

        // -------------------------
        // 2. Prevent duplicate bill for same customer/month
        // -------------------------
        $billingMonth = Carbon::parse($validated['billing_date'])->month;
        $billingYear = Carbon::parse($validated['billing_date'])->year;

        $existingBill = Bill::where('customer_id', $validated['customer_id'])
            ->whereMonth('billing_date', $billingMonth)
            ->whereYear('billing_date', $billingYear)
            ->first();

        if ($existingBill) {
            return response()->json([
                'success' => false,
                'message' => 'This customer already has a bill for this month and year.',
                'errors' => ['billing_date' => 'Duplicate bill']
            ], 422);
        }

        // -------------------------
        // 3. Create the bill
        // -------------------------
        $bill = Bill::create([
            'customer_id' => $validated['customer_id'],
            'meter_no' => $validated['meter_no'],
            'consumption' => $validated['consumption'],
            'total' => $validated['consumption'] * 10, // price per m³, adjust as needed
            'billing_date' => $validated['billing_date'],
            'due_date' => $validated['due_date'],
            'disconnection_date' => $validated['disconnection_date'],
            'status' => 'Unpaid', // default status
        ]);

        // -------------------------
        // 4. Create notification for customer
        // -------------------------
        Notification::create([
            'customer_id' => $bill->customer_id,
            'type' => 'bill_created',
            'message' => "Your bill for meter {$bill->meter_no} is ready. Due date: {$bill->due_date->format('M d, Y')}, Disconnection date: {$bill->disconnection_date->format('M d, Y')}.",
            'read' => false,
        ]);

        // -------------------------
        // 5. Return response with bill + customer
        // -------------------------
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
            'consumption' => 'required|numeric',
            'billing_date' => 'required|date',
            'due_date' => 'required|date',
            'disconnection_date' => 'required|date|after_or_equal:due_date',
        ]);

        // Prevent duplicate bill for same customer in same month/year (excluding current bill)
        $billingMonth = Carbon::parse($validated['billing_date'])->month;
        $billingYear = Carbon::parse($validated['billing_date'])->year;

        $existingBill = Bill::where('customer_id', $validated['customer_id'])
            ->whereMonth('billing_date', $billingMonth)
            ->whereYear('billing_date', $billingYear)
            ->where('id', '!=', $bill->id)
            ->first();

        if ($existingBill) {
            return response()->json([
                'message' => 'This customer already has a bill for this month and year.',
                'errors' => ['billing_date' => 'Duplicate bill']
            ], 422);
        }

        $bill->update([
            'customer_id' => $validated['customer_id'],
            'meter_no' => $validated['meter_no'],
            'consumption' => $validated['consumption'],
            'total' => $validated['consumption'] * 10,
            'billing_date' => $validated['billing_date'],
            'due_date' => $validated['due_date'],
            'disconnection_date' => $validated['disconnection_date'],
        ]);

        // Update notification if exists
        $notification = Notification::where('customer_id', $bill->customer_id)
            ->where('type', 'bill_created')
            ->where('message', 'like', "%meter {$bill->meter_no}%")
            ->first();

        if ($notification) {
            $notification->update([
                'message' => "Your bill for meter {$bill->meter_no} is updated. Due date: {$bill->due_date->format('M d, Y')}, Disconnection date: {$bill->disconnection_date->format('M d, Y')}.",
                'read' => false,
            ]);
        }

        return response()->json([
            'message' => 'Bill updated successfully!',
            'bill' => $bill->load('customer')
        ]);
    }

    /**
     * Delete a bill
     */
 
}