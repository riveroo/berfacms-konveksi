<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->query('status');

        $query = Transaction::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])->whereIn('status', ['waiting for payment', 'paid', 'on progress', 'done']);

        if ($status) {
            $query->where('status', $status);
        }

        // --- Overview Cards ---
        $overview = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('grand_total'),
            'total_customers' => $query->distinct('client_id')->count('client_id'),
            'avg_order_value' => $query->count() > 0 ? $query->avg('grand_total') : 0,
        ];

        // --- Daily Reports (Daily Orders & Revenue) ---
        $dailyStats = $query->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(id) as total_orders, SUM(grand_total) as total_revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // --- Product Performance ---
        $topProducts = TransactionDetail::whereHas('transaction', function ($q) use ($startDate, $endDate, $status) {
            $q->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])->whereIn('status', ['waiting for payment', 'paid', 'on progress', 'done']);
            
            if ($status) {
                $q->where('status', $status);
            }
        })
        ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
        ->with('product')
        ->groupBy('product_id')
        ->orderBy('total_qty', 'desc')
        ->limit(10)
        ->get();

        // --- Customer Report ---
        $topCustomers = $query->clone()
            ->select('client_id', DB::raw('COUNT(id) as total_orders'), DB::raw('SUM(grand_total) as total_spending'))
            ->with('client')
            ->groupBy('client_id')
            ->orderBy('total_spending', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.sales', compact(
            'overview',
            'dailyStats',
            'topProducts',
            'topCustomers',
            'startDate',
            'endDate',
            'status'
        ));
    }
}
