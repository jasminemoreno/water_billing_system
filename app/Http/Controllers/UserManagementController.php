<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;

class UserManagementController extends Controller
{
    // Fetch customers for User Management page
    public function index(Request $request)
    {
        $search = $request->get('search');

        $customers = Customer::when($search, function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('meter_no', 'like', "%{$search}%");
            })
            ->select(
                'id',
                'customer_name',
                'email',
                'status',
                'created_at'
            )
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($customers);
    }

    // Delete customer and all related data
    public function destroy($id)
    {
        try {

            $customer = Customer::findOrFail($id);

            // Delete payments first (because payments belong to bills/customer)
            Payment::where('customer_id', $customer->id)->delete();

            // Delete bills
            Bill::where('customer_id', $customer->id)->delete();

            // Delete customer account
            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}