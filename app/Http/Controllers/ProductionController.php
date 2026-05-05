<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\ProductionProduct;
use App\Models\Item;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Stock;
use App\Models\User;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StockIn;
use App\Models\StockOut;
use Exception;

class ProductionController extends Controller
{
    protected $stockService;

    public function __construct(StockMovementService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $search = $request->query('search');
        $userId = $request->query('user_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = Production::with(['user', 'materials', 'products'])
            ->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('production_name', 'like', "%{$search}%")
                  ->orWhere('batch_code', 'like', "%{$search}%");
            });
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($fromDate) {
            $query->whereDate('production_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('production_date', '<=', $toDate);
        }

        $productions = $query->paginate($perPage)->appends($request->query());
        $users = User::all();

        return view('production.index', compact('productions', 'users'));
    }

    public function show($id)
    {
        $production = Production::with([
            'user', 
            'materials.item', 
            'products.product', 
            'products.variant', 
            'products.sizeOption'
        ])->findOrFail($id);

        return view('production.show', compact('production'));
    }

    public function create()
    {
        // Batch Code Logic
        $todayStr = now()->format('dmY');
        $count = Production::whereDate('created_at', today())->count() + 1;
        $batchCode = $todayStr . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $materials = Item::whereHas('productType', function($q) {
            $q->where('name', 'Material');
        })->get();

        // If no items have productType 'Material', just get all items
        if ($materials->isEmpty()) {
            $materials = Item::all();
        }

        $products = Product::with(['variants.stocks.sizeOption'])->get();

        return view('production.create', compact('batchCode', 'materials', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_date' => 'required|date',
            'batch_code' => 'required|unique:productions,batch_code',
            'production_name' => 'required|string|max:255',
            'materials' => 'required|array|min:1',
            'materials.*.item_id' => 'required|exists:items,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.variant_id' => 'required|exists:variants,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Use selected date but combine with current time for full timestamp
            $productionTimestamp = \Illuminate\Support\Carbon::parse($request->production_date)->setTimeFrom(now());

            // 1. Create Production
            $production = Production::create([
                'production_date' => $productionTimestamp,
                'batch_code' => $request->batch_code,
                'production_name' => $request->production_name,
                'user_id' => auth()->id(),
            ]);

            // 2. Handle Materials (Stock Out)
            foreach ($request->materials as $matData) {
                if (empty($matData['item_id'])) continue;

                ProductionMaterial::create([
                    'production_id' => $production->id,
                    'item_id' => $matData['item_id'],
                    'quantity' => $matData['quantity'],
                ]);

                // Create Stock Out Record with full timestamp
                StockOut::create([
                    'production_id' => $production->id,
                    'trx_date' => $productionTimestamp,
                    'item_type' => 'material',
                    'item_id' => $matData['item_id'],
                    'quantity' => $matData['quantity'],
                    'user_id' => auth()->id(),
                ]);
            }

            // 3. Handle Products (Stock In)
            foreach ($request->products as $prodData) {
                if (empty($prodData['product_id'])) continue;

                ProductionProduct::create([
                    'production_id' => $production->id,
                    'product_id' => $prodData['product_id'],
                    'variant_id' => $prodData['variant_id'],
                    'size_option_id' => $prodData['size_option_id'] ?? null,
                    'quantity' => $prodData['quantity'],
                ]);

                // Create Stock In Record with full timestamp
                StockIn::create([
                    'production_id' => $production->id,
                    'trx_date' => $productionTimestamp,
                    'item_type' => 'product',
                    'product_id' => $prodData['product_id'],
                    'variant_id' => $prodData['variant_id'],
                    'size_option_id' => $prodData['size_option_id'] ?? null,
                    'quantity' => $prodData['quantity'],
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('production.index')
                ->with('success', 'Production recorded successfully and stock updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Production Save Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Failed to save production: ' . $e->getMessage());
        }
    }
}
