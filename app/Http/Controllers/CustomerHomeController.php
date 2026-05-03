<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerHomeController extends Controller
{
    /**
     * MAIN DASHBOARD (optional use)
     */
    public function dashboard(Request $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ FIXED: use customer_id ONLY
        $currentBill = Bill::where('customer_id', $customer->id)
            ->latest()
            ->first();

        $lastPayment = null;

        if ($currentBill) {
            $lastPayment = $currentBill->payments()
                ->latest()
                ->first();
        }

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'currentBill' => $currentBill,
            'lastPayment' => $lastPayment
        ]);
    }

    /**
     * DASHBOARD DATA (USED BY MOBILE APP)
     */
    public function dashboardData()
    {
        $customer = auth('customer-api')->user();

        if (!$customer) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // ✅ CURRENT BILL
        $currentBill = Bill::where('customer_id', $customer->id)
            ->latest()
            ->first();

        // ✅ LAST PAYMENT
        $lastPayment = Payment::where('customer_id', $customer->id)
            ->latest()
            ->first();

        // ==========================
        // 📊 WATER USAGE CHART DATA
        // ==========================

        $monthlyData = [];

        // initialize 12 months
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = 0;
        }

        // get ALL bills of customer
        $bills = Bill::where('customer_id', $customer->id)->get();
        foreach ($bills as $bill) {

            if (!$bill->billing_date)
                continue;

            $month = (int) date('m', strtotime($bill->billing_date));

            $monthlyData[$month] += (float) $bill->consumption;

        }

        // build chart arrays
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