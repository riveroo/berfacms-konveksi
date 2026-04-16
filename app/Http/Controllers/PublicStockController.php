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

        $query = Product::with(['variants.stocks', 'variants.productType'])
            ->where('is_active', true)
            ->orderBy('product_name');

        if ($typeId) {
            $query->whereHas('variants', function ($q) use ($typeId) {
                $q->where('product_type_id', $typeId);
            });
        }

        if ($search) {
            $query->where('product_name', 'like', "%{$search}%");
        }

        $products = $query->paginate(12);

        return view('public.stock', compact('sizes', 'products', 'productTypes', 'search', 'typeId'));
    }
}
