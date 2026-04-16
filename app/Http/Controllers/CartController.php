<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use App\Models\SizeOption;
use App\Models\Stock;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'size_option_id' => 'required|exists:size_options,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = Variant::with('product')->findOrFail($request->variant_id);
        $size = SizeOption::findOrFail($request->size_option_id);
        
        $stock = Stock::where('variant_id', $request->variant_id)
            ->where('size_option_id', $request->size_option_id)
            ->first();

        if (!$stock || $stock->stock < $request->quantity) {
            return response()->json(['message' => 'Stok tidak mencukupi'], 422);
        }

        $cart = session()->get('cart', []);
        $cartId = "{$request->variant_id}-{$request->size_option_id}";

        if (isset($cart[$cartId])) {
            $newQuantity = $cart[$cartId]['quantity'] + $request->quantity;
            if ($newQuantity > $stock->stock) {
                return response()->json(['message' => 'Total kuantitas melebihi stok tersedia'], 422);
            }
            $cart[$cartId]['quantity'] = $newQuantity;
        } else {
            $cart[$cartId] = [
                'product_id' => $variant->product_id,
                'product_name' => $variant->product->product_name,
                'variant_id' => $variant->id,
                'variant_name' => $variant->variant_name,
                'size_option_id' => $size->id,
                'size_name' => $size->name,
                'price' => $stock->price,
                'quantity' => $request->quantity,
                'image' => $variant->image ?: $variant->product->thumbnail,
                'max_stock' => $stock->stock
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Berhasil ditambahkan ke troli',
            'cart_count' => count($cart)
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_id])) {
            $item = $cart[$request->cart_id];
            $cart[$request->cart_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            if ($request->ajax()) {
                $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
                return response()->json([
                    'success' => true,
                    'subtotal' => 'Rp' . number_format($item['price'] * $request->quantity, 0, ',', '.'),
                    'total_price' => 'Rp' . number_format($total, 0, ',', '.'),
                    'total_items' => count($cart)
                ]);
            }
        }

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_id])) {
            unset($cart[$request->cart_id]);
            session()->put('cart', $cart);

            if ($request->ajax()) {
                $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
                return response()->json([
                    'success' => true,
                    'total_price' => 'Rp' . number_format($total, 0, ',', '.'),
                    'total_items' => count($cart),
                    'is_empty' => count($cart) === 0
                ]);
            }
        }

        return back()->with('success', 'Item dihapus dari keranjang');
    }
}
