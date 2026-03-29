<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Payment;

class ReportController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $bills = Bill::with(['customer','payments'])->get();

        $payments = Payment::with(['customer','bill'])
            ->whereYear('created_at',$currentYear)
            ->whereMonth('created_at',$currentMonth)
            ->whereIn('status',['Approved','Verified'])
            ->orderByDesc('created_at')
            ->get();

        $unpaidBills = Bill::with('customer')
            ->whereDoesntHave('payments',function($q){
                $q->whereIn('status',['Approved','Verified']);
            })
            ->get();

        $monthlyRevenueData = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereIn('status',['Approved','Verified'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenue = [];
        for($m=1;$m<=12;$m++) $monthlyRevenue[$m]=0;
        foreach($monthlyRevenueData as $d){
            $monthlyRevenue[$d->month] = $d->total;
        }

        return response()->json([
            'bills'=>$bills,
            'payments'=>$payments,
            'unpaidBills'=>$unpaidBills,
            'monthlyRevenue'=>$monthlyRevenue
        ]);
    }

    public function paymentHistoryByMonth($year,$month)
    {
        $payments = Payment::withTrashed()
            ->with(['customer','bill'])
            ->whereYear('created_at',$year)
            ->whereMonth('created_at',$month)
            ->whereIn('status',['Approved','Verified'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'payments'=>$payments
        ]);
    }
        public function billHistoryByMonth($year, $month)
    {
       $bills = Bill::withTrashed()
            ->with(['customer', 'payments'])
            ->whereNotNull('billing_date')
            ->whereYear('billing_date', (int) $year)
            ->whereMonth('billing_date', (int) $month)
            ->orderByDesc('billing_date')
            ->get();

        return response()->json([
            'bills'=>$bills
        ]);
        

    }
    public function rejectedPaymentsByMonth($year, $month)
{
    $payments = Payment::withTrashed()

        ->with(['customer','bill'])
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->where('status', 'Rejected') // 🔥 ONLY REJECTED
        ->orderByDesc('created_at')
        ->get();

    return response()->json([
        'payments' => $payments
    ]);
}

}