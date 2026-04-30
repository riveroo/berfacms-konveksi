<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $transactions = [];
        $isFiltered = false;

        if ($request->has('show_data')) {
            $isFiltered = true;
            $transactions = $this->getFilteredData($request);
        }

        return view('admin.sales-report.index', compact('transactions', 'isFiltered'));
    }

    public function export(Request $request)
    {
        return Excel::download(new SalesReportExport($this->getFilteredData($request)), 'sales_report_' . date('Y-m-d') . '.xlsx');
    }

    private function getFilteredData(Request $request)
    {
        $query = Transaction::with(['client', 'details', 'payments', 'logs.user']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('item_status')) {
            $query->whereHas('details', function($q) use ($request) {
                $q->where('item_status', $request->item_status);
            });
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Return all results (no pagination)
        return $query->orderBy('created_at', 'desc')->get();
    }
}
