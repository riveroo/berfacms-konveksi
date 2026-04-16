<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreOrder;

class PreOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PreOrder::with('client')->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->whereHas('client', function ($q2) use ($search) {
                $q2->where('client_name', 'like', "%{$search}%")
                   ->orWhere('phone_number', 'like', "%{$search}%");
            });
        });

        $query->when($request->filled('po_id'), function ($q) use ($request) {
            $q->where('po_id', 'like', '%' . $request->po_id . '%');
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
        $preOrders = $query->paginate($perPage)->withQueryString();

        return view('admin.pre_orders.index', compact('preOrders'));
    }

    public function create()
    {
        $clients = \App\Models\Client::all();
        $products = \App\Models\Product::with(['variants.stocks.sizeOption'])->get();
        return view('admin.pre_orders.create', compact('clients', 'products'));
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

            // Calculate totals defensively
            $totalPrice = 0;
            $itemsDiscount = 0;
            
            foreach ($request->items as $item) {
                $totalPrice += ($item['price'] * $item['qty']);
                $itemsDiscount += $item['discount'];
            }
            
            $overallDiscount = $request->overall_discount ?? 0;
            $grandTotal = $totalPrice - ($itemsDiscount + $overallDiscount);

            $preOrder = PreOrder::create([
                'po_id' => 'PO-' . strtoupper(uniqid()),
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => $itemsDiscount + $overallDiscount,
                'grand_total' => $grandTotal > 0 ? $grandTotal : 0,
                'status' => 'on process',
            ]);

            foreach ($request->items as $item) {
                \App\Models\PreOrderDetail::create([
                    'pre_order_id' => $preOrder->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'discount' => $item['discount'],
                    'subtotal' => ($item['price'] * $item['qty']) - $item['discount'],
                ]);

                // We do NOT decrement stock for RFQ/Pre Orders until accepted/converted.
            }
        });

        return response()->json(['success' => true]);
    }

    public function detail($id)
    {
        $preOrder = PreOrder::with(['client', 'details.product', 'details.variant', 'details.sizeOption', 'transaction'])->findOrFail($id);
        return view('admin.pre_orders.detail', compact('preOrder'));
    }

    public function reject(Request $request, $id)
    {
        $preOrder = PreOrder::findOrFail($id);
        $preOrder->update(['status' => 'rejected']);
        
        return back()->with('success', 'Pre Order rejected successfully.');
    }

    public function accept(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:waiting for payment,paid',
        ]);

        $preOrder = PreOrder::with(['details'])->findOrFail($id);
        
        if ($preOrder->status !== 'on process') {
            return back()->with('error', 'Pre order is already processed.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($preOrder, $request) {
            $transaction = \App\Models\Transaction::create([
                'trx_id' => 'TRX-' . strtoupper(uniqid()),
                'client_id' => $preOrder->client_id,
                'total_price' => $preOrder->total_price,
                'total_discount' => $preOrder->total_discount,
                'grand_total' => $preOrder->grand_total,
                'status' => $request->payment_status,
            ]);

            foreach ($preOrder->details as $item) {
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'size_option_id' => $item->size_option_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'discount' => $item->discount,
                    'subtotal' => $item->subtotal,
                ]);

                // Update stock
                $stock = \App\Models\Stock::where('variant_id', $item->variant_id)
                    ->where('size_option_id', $item->size_option_id)
                    ->first();
                if ($stock) {
                    $stock->decrement('stock', $item->quantity);
                }
            }

            $preOrder->update([
                'status' => 'accepted',
                'transaction_id' => $transaction->id
            ]);
        });

        return back()->with('success', 'Pre Order accepted and converted to Transaction.');
    }
}
