<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // List all customers (JSON for Vue)
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    // Add a new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'meter_no' => 'required|unique:customers,meter_no',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Auto-generate a password
        $password = Str::random(8);

        $customer = Customer::create([
            'customer_name' => $validated['customer_name'],
            'email' => $validated['email'],
            'meter_no' => $validated['meter_no'],
            'status' => $validated['status'],
            'password' => bcrypt($password),
        ]);

        return response()->json([
            'message' => 'Customer added successfully!',
            'password' => $password,
            'customer' => $customer
        ]);
    }

    // Update existing customer
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $id,
            'meter_no' => 'required|unique:customers,meter_no,' . $id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Customer updated successfully!',
            'customer' => $customer
        ]);
    }

    // Optional: search customers by name or meter_no (for live search)
    public function search(Request $request)
    {
        $query = $request->get('query', '');

        $customers = Customer::where('customer_name', 'like', "%$query%")
            ->orWhere('meter_no', 'like', "%$query%")
            ->get();

        return response()->json($customers);
    }
}