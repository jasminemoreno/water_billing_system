<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerHomeController extends Controller
{
    public function dashboard(Request $request)
{
    $customer = $request->user(); // This should return the authenticated customer

    if (!$customer) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    $currentBill = Bill::where('meter_no', $customer->meter_no)
        ->latest('created_at')
        ->first();

    $lastPayment = null;

    if ($currentBill) {
        $lastPayment = $currentBill->payments()
            ->latest('created_at')
            ->first();
    }

    return response()->json([
        'success' => true,
        'customer' => $customer,
        'currentBill' => $currentBill,
        'lastPayment' => $lastPayment
    ]);
}
    // JSON endpoint for AJAX updates
    public function dashboardData()
    {
        $customer = auth('customer')->user();

        $currentBill = Bill::where('customer_id', $customer->id)->latest()->first();
        $lastPayment = Payment::where('customer_id', $customer->id)->latest()->first();

        // Chart data
        $bills = Bill::where('customer_id', $customer->id)->get();
        $monthlyData = collect();
        for ($i = 1; $i <= 12; $i++) $monthlyData[$i] = 0;
        foreach ($bills as $bill) {
            $month = (int) $bill->created_at->format('m');
            $monthlyData[$month] += $bill->consumption;
        }

        $chartLabels = collect(range(1,12))->map(fn($m) => date('M', mktime(0,0,0,$m,1)));
        $chartData   = $monthlyData->values();
        $chartColors = $chartData->map(fn($val) => $val > 0 ? 'rgba(54, 162, 235, 0.6)' : 'rgba(200,200,200,0.3)');

        return response()->json([
            'currentBill' => $currentBill,
            'lastPayment' => $lastPayment,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartColors' => $chartColors,
        ]);
    }
}
