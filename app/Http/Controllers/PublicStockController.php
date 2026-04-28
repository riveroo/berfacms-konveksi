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

        $sizes = SizeOption::ordered()->get();
        $productTypes = ProductType::orderBy('name')->get();

        $query = \App\Models\Variant::with(['product', 'productType', 'stocks'])
            ->whereHas('product', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy(
                \App\Models\Product::select('product_name')
                    ->whereColumn('products.id', 'variants.product_id')
                    ->limit(1)
            );

        if ($typeId) {
            $query->where('product_type_id', $typeId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($q2) use ($search) {
                    $q2->where('product_name', 'like', "%{$search}%");
                })
                ->orWhere('variant_name', 'like', "%{$search}%")
                ->orWhere('variant_code', 'like', "%{$search}%");
            });
        }

        $variants = $query->paginate(25)->withQueryString();

        return view('public.stock', compact('sizes', 'variants', 'productTypes', 'search', 'typeId'));
    }
}
