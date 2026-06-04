<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SizeOption;
use App\Models\ProductType;

class PublicStockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $typeId = $request->query('type_id');
        $productId = $request->query('product_id');

        $sizes = SizeOption::ordered()->get();
        $productTypes = ProductType::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        $query = \App\Models\Variant::with(['product', 'productType', 'stocks'])
            ->whereHas('product', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy(
                \App\Models\Product::select('sort_order')
                    ->whereColumn('products.id', 'variants.product_id')
                    ->limit(1),
                'asc'
            )
            ->orderBy(
                \App\Models\Product::select('created_at')
                    ->whereColumn('products.id', 'variants.product_id')
                    ->limit(1),
                'desc'
            )
            ->orderBy('variant_name', 'asc');

        if ($typeId) {
            $query->where('product_type_id', $typeId);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($search) {
            $query->where('variant_name', 'like', "%{$search}%");
        }

        $variantsList = \App\Models\Variant::with('product')
            ->whereHas('product', function($q) {
                $q->where('is_active', true);
            })
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'variant_name' => $v->variant_name,
                'product_name' => $v->product?->product_name ?? '',
                'variant_code' => $v->variant_code ?? ''
            ]);

        $variants = $query->paginate(25)->withQueryString();

        return view('public.stock', compact('sizes', 'variants', 'productTypes', 'products', 'search', 'typeId', 'productId', 'variantsList'));
    }
}
