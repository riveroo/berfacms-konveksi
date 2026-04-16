<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variant;
use App\Models\SizeOption;

class CekStokController extends Controller
{
    public function product(Request $request)
    {
        $search = $request->query('search');
        $productId = $request->query('product_id');

        $sizes = SizeOption::ordered()->get();
        $products = \App\Models\Product::orderBy('product_name')->get();

        $query = Variant::with(['product', 'productType', 'stocks']);

        if ($productId) {
            $query->where('product_id', $productId);
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

        $variants = $query->get();

        return view('admin.cek-stok.product', compact('sizes', 'variants', 'products', 'search', 'productId'));
    }

    public function barang()
    {
        return view('admin.cek-stok.barang');
    }
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StockTemplateExport, 'stock_template.xlsx');
    }

    public function importStock(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StockUpdateImport, $request->file('file'));
            \Filament\Notifications\Notification::make()
                ->title('Stock updated successfully!')
                ->success()
                ->send();
            return redirect()->back();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Import Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return redirect()->back();
        }
    }
}
