<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Variant;
use App\Models\SizeOption;
use App\Models\ProductType;
use App\Models\Product;
use App\Exports\ProductPricingTemplateExport;
use App\Imports\ProductPricingImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ProductPricingController extends Controller
{
    /**
     * Display a listing of product pricing records.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $productId = $request->query('product_id');

        $products = Product::orderBy('product_name')->get();
        $sizes = SizeOption::ordered()->get();

        // Query stocks directly to manage COGS & Selling Price
        $query = Stock::with(['variant.product', 'variant.productType', 'sizeOption']);

        if ($productId) {
            $query->whereHas('variant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        if ($search) {
            $query->whereHas('variant', function ($q) use ($search) {
                $q->whereHas('product', function ($q2) use ($search) {
                    $q2->where('product_name', 'like', "%{$search}%");
                })
                ->orWhere('variant_name', 'like', "%{$search}%")
                ->orWhere('variant_code', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('perPage', 10);
        $stocks = $query->paginate($perPage)->withQueryString();

        return view('admin.product-pricing.index', compact('stocks', 'products', 'sizes', 'search', 'productId'));
    }

    /**
     * Update a single stock pricing record (COGS & Selling Price).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cogs' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $stock = Stock::findOrFail($id);
        $stock->update([
            'cogs' => floatval($request->cogs),
            'price' => floatval($request->price),
        ]);

        Notification::make()
            ->title('Pricing updated successfully!')
            ->success()
            ->send();

        return redirect()->back();
    }

    /**
     * Export all stocks as an Excel template.
     */
    public function downloadTemplate()
    {
        return Excel::download(new ProductPricingTemplateExport, 'product_pricing_template.xlsx');
    }

    /**
     * Bulk import and validate pricing records.
     */
    public function importPricing(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new ProductPricingImport();
            Excel::import($import, $request->file('file'));

            $updated = $import->updatedCount;
            $skipped = $import->skippedCount;

            $message = "Successfully updated: {$updated} rows. Skipped: {$skipped} rows due to mismatch.";

            if ($skipped > 0) {
                Notification::make()
                    ->title('Import Completed with Warnings')
                    ->body($message)
                    ->warning()
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->title('Import Successful')
                    ->body($message)
                    ->success()
                    ->send();
            }

            return redirect()->back();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return redirect()->back();
        }
    }
}
