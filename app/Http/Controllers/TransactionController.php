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

    public function report()
    {
        return view('admin.transactions.report');
    }

    public function detail($id)
    {
        $transaction = Transaction::with(['client', 'details.product', 'details.variant', 'details.sizeOption'])->findOrFail($id);
        return view('admin.transactions.detail', compact('transaction'));
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
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
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

    public function create()
    {
        $clients = \App\Models\Client::all();
        $products = \App\Models\Product::with(['variants.stocks.sizeOption'])->get();
        return view('admin.transactions.create', compact('clients', 'products'));
    }

    public function store(Request $request, \App\Services\CreateTransactionService $createTransactionService)
    {
        $request->validate([
            'client_phone' => 'required|string',
            'client_name' => 'required|string',
            'transaction_type' => 'required|in:pre_order,direct_order',
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
            if ($request->transaction_type === 'pre_order' && $result['preOrder']) {
                $redirectUrl = route('pre-orders.detail', $result['preOrder']->id);
            }

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
