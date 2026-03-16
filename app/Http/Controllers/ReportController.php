<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------
        | 1. Get ALL Bills with customer & payments
        |--------------------------------------------
        */
        $bills = Bill::with(['customer', 'payments'])->get();


        /*
        |--------------------------------------------
        | 2. Get Payments (Cash + Online)
        | Cash = Verified
        | Online = Approved
        |--------------------------------------------
        */
        $payments = Payment::with(['customer', 'bill'])
            ->whereIn('status', ['Approved', 'Verified'])
            ->orderByDesc('created_at')
            ->get();


        /*
        |--------------------------------------------
        | 3. Unpaid Bills
        | Bills without verified/approved payment
        |--------------------------------------------
        */
        $unpaidBills = Bill::with('customer')
            ->whereDoesntHave('payments', function ($query) {
                $query->whereIn('status', ['Approved', 'Verified']);
            })
            ->get();


        /*
        |--------------------------------------------
        | 4. Monthly Revenue (Cash + Online)
        |--------------------------------------------
        */
        $monthlyRevenueData = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereIn('status', ['Approved', 'Verified'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();


        /*
        |--------------------------------------------
        | 5. Initialize 12 months revenue
        |--------------------------------------------
        */
        $monthlyRevenue = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyRevenue[$m] = 0;
        }

        foreach ($monthlyRevenueData as $data) {
            $monthlyRevenue[$data->month] = $data->total;
        }


        /*
        |--------------------------------------------
        | 6. Return JSON to Vue frontend
        |--------------------------------------------
        */
        return response()->json([
            'bills' => $bills,
            'payments' => $payments,
            'unpaidBills' => $unpaidBills,
            'monthlyRevenue' => $monthlyRevenue
        ]);
    }
}