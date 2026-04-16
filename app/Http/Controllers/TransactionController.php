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

    public function cancel(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status' => 'cancelled']);
        
        // Optional: you can restore stock here if you want:
        // foreach ($transaction->details as $detail) {
        //     \App\Models\Stock::where('variant_id', $detail->variant_id)
        //          ->where('size_option_id', $detail->size_option_id)
        //          ->increment('stock', $detail->quantity);
        // }

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function inputPayment(Request $request, $id)
    {
        $request->validate([
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'transfer_amount' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'status' => 'paid',
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'transfer_amount' => $request->transfer_amount,
        ]);

        return back()->with('success', 'Payment inputted successfully.');
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

        return back()->with('success', 'Status updated successfully.');
    }

    public function create()
    {
        $clients = \App\Models\Client::all();
        $products = \App\Models\Product::with(['variants.stocks.sizeOption'])->get();
        return view('admin.transactions.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_phone' => 'required|string',
            'client_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.size_option_id' => 'required|exists:size_options,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $client = \App\Models\Client::firstOrCreate(
                ['phone_number' => $request->client_phone],
                [
                    'client_name' => $request->client_name,
                    'information' => $request->client_info
                ]
            );

            // Calculate totals defensively to ensure they match backend limits
            $totalPrice = 0;
            $itemsDiscount = 0;
            
            foreach ($request->items as $item) {
                $totalPrice += ($item['price'] * $item['qty']);
                $itemsDiscount += $item['discount'];
            }
            
            $overallDiscount = $request->overall_discount ?? 0;
            $grandTotal = $totalPrice - ($itemsDiscount + $overallDiscount);

            $transaction = Transaction::create([
                'trx_id' => 'TRX-' . strtoupper(uniqid()),
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => $itemsDiscount + $overallDiscount,
                'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
                'status' => 'waiting for payment',
            ]);

            foreach ($request->items as $item) {
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => $item['discount'],
                    'subtotal' => ($item['price'] * $item['qty']) - $item['discount'],
                ]);

                // Optional: Update stock here
                $stock = \App\Models\Stock::where('variant_id', $item['variant_id'])
                    ->where('size_option_id', $item['size_option_id'])
                    ->first();
                if ($stock) {
                    $stock->decrement('stock', $item['qty']);
                }
            }
        });

        return response()->json(['success' => true]);
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

    public function update(Request $request, $id)
    {
        $transaction = \App\Models\Transaction::with('details')->findOrFail($id);
        if (in_array($transaction->status, ['done', 'cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Cannot edit transaction.']);
        }

        $request->validate([
            'client_phone' => 'required|string',
            'client_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'required|exists:variants,id',
            'items.*.size_option_id' => 'required|exists:size_options,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $transaction) {
            $client = \App\Models\Client::firstOrCreate(
                ['phone_number' => $request->client_phone],
                [
                    'client_name' => $request->client_name,
                    'information' => $request->client_info
                ]
            );

            // Restore stocks from old details
            foreach ($transaction->details as $oldDetail) {
                \App\Models\Stock::where('variant_id', $oldDetail->variant_id)
                    ->where('size_option_id', $oldDetail->size_option_id)
                    ->increment('stock', $oldDetail->quantity);
            }
            // Delete old details
            $transaction->details()->delete();

            $totalPrice = 0;
            $itemsDiscount = 0;
            
            foreach ($request->items as $item) {
                $totalPrice += ($item['price'] * $item['qty']);
                $itemsDiscount += $item['discount'];
            }
            
            $overallDiscount = $request->overall_discount ?? 0;
            $grandTotal = $totalPrice - ($itemsDiscount + $overallDiscount);

            $transaction->update([
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => $itemsDiscount + $overallDiscount,
                'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
            ]);

            foreach ($request->items as $item) {
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => $item['discount'],
                    'subtotal' => ($item['price'] * $item['qty']) - $item['discount'],
                ]);

                // Reduce stock again
                $stock = \App\Models\Stock::where('variant_id', $item['variant_id'])
                    ->where('size_option_id', $item['size_option_id'])
                    ->first();
                if ($stock) {
                    $stock->decrement('stock', $item['qty']);
                }
            }
        });

        return response()->json(['success' => true]);
    }
}
