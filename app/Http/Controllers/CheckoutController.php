<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += ($item['price'] * $item['quantity']);
        }

        $clients = Client::all();

        return view('checkout.index', compact('cart', 'totalPrice', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'client_name' => 'required|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong');
        }

        DB::transaction(function () use ($request, $cart) {
            $client = Client::firstOrCreate(
                ['phone_number' => $request->phone_number],
                ['client_name' => $request->client_name]
            );

            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += ($item['price'] * $item['quantity']);
            }

            // Assume no overall discount for frontend cart for now, or fetch from request if any
            $grandTotal = $totalPrice;

            $transaction = Transaction::create([
                'trx_id' => 'TRX-' . strtoupper(uniqid()),
                'client_id' => $client->id,
                'total_price' => $totalPrice,
                'total_discount' => 0,
                'grand_total' => $grandTotal,
                'status' => 'waiting for payment',
            ]);

            foreach ($cart as $id => $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'size_option_id' => $item['size_option_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'discount' => 0,
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Update stock conditionally
                $stock = \App\Models\Stock::where('variant_id', $item['variant_id'])
                    ->where('size_option_id', $item['size_option_id'])
                    ->first();
                if ($stock) {
                    $stock->decrement('stock', $item['quantity']);
                }
            }
            
            // store transaction info to session for success page
            session([
                'checkout_success_trx_id' => $transaction->trx_id,
                'checkout_success_name' => $client->client_name,
                'checkout_success_total' => $grandTotal,
            ]);

            // Clear the cart
            session()->forget('cart');
        });

        return redirect()->route('checkout.success');
    }

    public function success()
    {
        if (!session()->has('checkout_success_trx_id')) {
            return redirect('/');
        }

        return view('checkout.success');
    }

    public function invoice($trx_id)
    {
        $transaction = Transaction::where('trx_id', $trx_id)
            ->with(['client', 'details.product', 'details.variant', 'details.sizeOption'])
            ->firstOrFail();

        return view('checkout.invoice', compact('transaction'));
    }
}
