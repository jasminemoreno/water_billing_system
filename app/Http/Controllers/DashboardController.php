<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ================================
        // YEAR FILTER (default = current year)
        // ================================
        $year = $request->get('year', Carbon::now()->year);

        // ================================
        // TOTAL CUSTOMERS
        // ================================
        $totalCustomers = Customer::count();

        // ================================
        // TOTAL BILLS (SUM OF TOTAL COLUMN)
        // ================================
        $totalBills = Bill::sum('total');

        // ================================
        // TOTAL PAYMENTS (SUM OF AMOUNT COLUMN)
        // ================================
        $totalPayments = Payment::sum('amount');

        // ================================
        // MONTHLY REVENUE (OPTIMIZED QUERY)
        // ================================
        $monthlyRevenueRaw = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('status', 'Verified')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        $monthlyRevenue = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyRevenue[] = $monthlyRevenueRaw[$month] ?? 0;
        }

        // ================================
        // PAID PAYMENTS COUNT
        // ================================
        $paidCount = Payment::where('status', 'Verified')->count();

        // ================================
        // UNPAID BILLS (NO VERIFIED PAYMENT)
        // ================================
        $unpaidCount = Bill::whereDoesntHave('payments', function ($query) {
            $query->where('status', 'Verified');
        })->count();

        // ================================
        // RETURN JSON RESPONSE
        // ================================
        return response()->json([
            'totalCustomers' => $totalCustomers,
            'totalBills' => $totalBills,
            'totalPayments' => $totalPayments,
            'monthlyRevenue' => $monthlyRevenue,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
            'year' => $year
        ]);
    }
}