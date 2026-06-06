<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('client')->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->whereHas('client', function ($q2) use ($search) {
                $q2->where('client_name', 'like', "%{$search}%")
                   ->orWhere('phone_number', 'like', "%{$search}%");
            });
        });

        $query->when($request->filled('trx_id'), function ($q) use ($request) {
            $q->where('trx_id', 'like', '%' . $request->trx_id . '%');
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('payment_status'), function ($q) use ($request) {
            $q->where('payment_status', $request->payment_status);
        });

        $query->when($request->filled('transaction_type'), function ($q) use ($request) {
            $q->where('transaction_type', $request->transaction_type);
        });

        $query->when($request->filled('item_status'), function ($q) use ($request) {
            $q->where('item_status', $request->item_status);
        });

        $query->when($request->filled('date_from'), function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->date_from);
        });

        $query->when($request->filled('date_to'), function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->date_to);
        });

        $perPage = $request->query('perPage', 10);
        $transactions = $query->paginate($perPage)->withQueryString();

        return view('admin.transactions.index', compact('transactions'));
    }

    public function report(Request $request)
    {
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $startDate = $startDateInput ? \Carbon\Carbon::parse($startDateInput)->startOfDay() : now()->startOfMonth();
        $endDate = $endDateInput ? \Carbon\Carbon::parse($endDateInput)->endOfDay() : now()->endOfMonth();

        // 1. Overview Metrics
        $totalRevenue = Transaction::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

        $totalProductsSold = \App\Models\TransactionDetail::whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->where('status', '!=', 'cancelled')->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('quantity');

        $avgOrderValue = Transaction::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('grand_total') ?: 0;

        // 2. Daily Sales Trend
        $dailyStats = Transaction::select(
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('SUM(grand_total) as total')
            )
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $trendLabels = [];
        $trendData = [];
        foreach ($dailyStats as $stat) {
            $trendLabels[] = \Carbon\Carbon::parse($stat->date)->format('d M');
            $trendData[] = floatval($stat->total);
        }
        if (empty($trendLabels)) {
            $trendLabels[] = now()->format('d M');
            $trendData[] = 0;
        }

        // 3. Top Selling Products
        $topProducts = \App\Models\TransactionDetail::select(
                'product_id',
                \DB::raw('SUM(quantity) as total_qty'),
                \DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->where('status', '!=', 'cancelled')->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // 4. Top Customers (Spenders)
        $topCustomers = Transaction::select(
                'client_id',
                \DB::raw('COUNT(id) as total_orders'),
                \DB::raw('SUM(grand_total) as total_spending')
            )
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('client_id')
            ->with('client')
            ->groupBy('client_id')
            ->orderBy('total_spending', 'desc')
            ->take(5)
            ->get();

        return view('admin.transactions.report', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'totalRevenue' => $totalRevenue,
            'totalProductsSold' => $totalProductsSold,
            'avgOrderValue' => $avgOrderValue,
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
        ]);
    }

    public function detail($id)
    {
        $transaction = Transaction::with(['client', 'details.product', 'details.variant', 'details.sizeOption'])->findOrFail($id);
        $transferToAccounts = \App\Models\Account::where('type', 'asset')->where('is_active', true)->get();
        $categories = \App\Models\Account::where('type', 'revenue')->where('is_active', true)->get();
        return view('admin.transactions.detail', compact('transaction', 'transferToAccounts', 'categories'));
    }

    public function cancel(Request $request, $id, \App\Services\UpdateTransactionService $updateTransactionService)
    {
        $transaction = Transaction::findOrFail($id);
        $updateTransactionService->cancel($transaction);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function inputPayment(Request $request, $id, \App\Services\PaymentService $paymentService)
    {
        $request->validate([
            'account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'transfer_to_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:accounts,id',
        ]);

        $transaction = Transaction::findOrFail($id);
        
        try {
            $paymentService->createPayment($transaction, $request->all());
            return back()->with('success', 'Payment inputted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:on progress,done',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => $request->status,
        ]);

        \App\Models\TransactionLog::create([
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
            'action' => 'Changed transaction status',
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function updateDeadline(Request $request, $id)
    {
        $request->validate([
            'deadline' => 'required|date',
        ]);

        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->payment_status === 'paid') {
            return back()->withErrors(['deadline' => 'Cannot update deadline for fully paid transactions.']);
        }

        $transaction->update([
            'deadline' => $request->deadline,
        ]);

        \App\Models\TransactionLog::create([
            'transaction_id' => $transaction->id,
            'user_id' => auth()->id(),
            'action' => 'Updated deadline to ' . $request->deadline,
        ]);

        return back()->with('success', 'Deadline updated successfully.');
    }

    public function create()
    {
        $clients = \App\Models\Client::all();
        $products = \App\Models\Product::with(['variants.stocks.sizeOption', 'variants.productType'])->get();
        return view('admin.transactions.create', compact('clients', 'products'));
    }

    public function store(Request $request, \App\Services\CreateTransactionService $createTransactionService)
    {
        $request->validate([
            'client_phone' => 'required|string',
            'client_name' => 'required|string',
            'transaction_type' => 'required|in:pre_order,direct_order',
            'overall_discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.size_option_id' => 'required|exists:size_options,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            $result = $createTransactionService->execute($request->all());
            
            $redirectUrl = route('transactions.detail', $result['transaction']->id);

            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function edit($id)
    {
        $transaction = \App\Models\Transaction::with(['client', 'details.product', 'details.variant', 'details.sizeOption'])->findOrFail($id);
        
        if (in_array($transaction->status, ['done', 'cancelled'])) {
            \Filament\Notifications\Notification::make()
                ->title('Cannot edit')
                ->body('Transaction is already done or cancelled.')
                ->danger()
                ->send();
            return redirect()->route('transactions.detail', $id);
        }
        
        $clients = \App\Models\Client::all();
        $products = \App\Models\Product::with('variants.stocks.sizeOption')->get();

        return view('admin.transactions.edit', compact('transaction', 'clients', 'products'));
    }

    public function update(Request $request, $id, \App\Services\UpdateTransactionService $updateTransactionService)
    {
        $transaction = \App\Models\Transaction::with('details')->findOrFail($id);
        if (in_array($transaction->status, ['done', 'cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Cannot edit transaction.']);
        }

        $request->validate([
            'client_phone' => 'required|string',
            'client_name' => 'required|string',
            'transaction_type' => 'required|in:pre_order,direct_order',
            'item_status' => 'required|string',
            'overall_discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.size_option_id' => 'required|exists:size_options,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            $updateTransactionService->update($transaction, $request->all());

            return response()->json([
                'success' => true,
                'redirect_url' => route('transactions.detail', $transaction->id)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }
}
