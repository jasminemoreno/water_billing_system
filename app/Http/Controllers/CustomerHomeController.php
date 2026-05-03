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
        $customer = auth('customer-api')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ✅ FIX: use meter_no (same as dashboard())
        $currentBill = Bill::where('meter_no', $customer->meter_no)
            ->latest()
            ->first();

        $lastPayment = Payment::where('customer_id', $customer->id)
            ->latest()
            ->first();

        // FIX chart data (also meter_no)
        $monthlyData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = 0;
        }

        $bills = Bill::where('meter_no', $customer->meter_no)->get();

        foreach ($bills as $bill) {
            $month = (int) $bill->created_at->format('m');
            $monthlyData[$month] += (float) $bill->consumption;
        }

        $chartLabels = [];
        $chartData = [];
        $chartColors = [];

        foreach ($monthlyData as $monthNum => $consumption) {
            $chartLabels[] = date('M', mktime(0, 0, 0, $monthNum, 1));
            $chartData[] = $consumption;
            $chartColors[] = $consumption > 0
                ? 'rgba(54,162,235,0.6)'
                : 'rgba(200,200,200,0.3)';
        }

        return response()->json([
            'currentBill' => $currentBill,
            'lastPayment' => $lastPayment,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'chartColors' => $chartColors,
        ]);
    }
}
