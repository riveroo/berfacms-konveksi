<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\ProductType;

class InventoryOverviewController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $supplierId = $request->query('supplier_id');
        $productTypeId = $request->query('product_type_id');

        $suppliers = Supplier::orderBy('name')->get();
        $productTypes = ProductType::orderBy('name')->get();

        $query = Item::with(['supplier', 'unit', 'productType'])
            ->orderByRaw('(stock - minimum_stock) ASC')
            ->orderBy('item_name');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($productTypeId) {
            $query->where('product_type_id', $productTypeId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        return view('inventory.overview', compact('items', 'suppliers', 'productTypes', 'search', 'supplierId', 'productTypeId'));
    }

    public function export(Request $request)
    {
        $search = $request->query('search');
        $supplierId = $request->query('supplier_id');
        $productTypeId = $request->query('product_type_id');

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InventoryOverviewExport($search, $supplierId, $productTypeId), 'inventory_overview.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\InventoryStockTemplateExport(), 'inventory_stock_template.xlsx');
    }

    public function import(Request $request, \App\Services\InventoryImportService $service)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\InventoryStockImport($service), $request->file('file'));
            
            $results = $service->getResults();
            $total = $results['success'] + $results['failed'];
            
            $body = __('inventory.total_processed', ['total' => $total]) . "<br>" . 
                    __('inventory.successfully_updated', ['success' => $results['success']]) . "<br>" . 
                    __('inventory.failed_rows', ['failed' => $results['failed']]);
                    
            if ($results['failed'] > 0) {
                // Formatting error list
                $errorList = implode("<br>", array_map(function($err) {
                    return "Row {$err['row']}: {$err['reason']}";
                }, array_slice($results['errors'], 0, 5)));
                
                if (count($results['errors']) > 5) {
                    $errorList .= "<br>... and more.";
                }
                
                $body .= "<br><br><b>" . __('inventory.errors_label') . "</b><br>" . $errorList;

                \Filament\Notifications\Notification::make()
                    ->title(__('inventory.import_completed_errors'))
                    ->body($body)
                    ->warning()
                    ->persistent()
                    ->send();
            } else {
                \Filament\Notifications\Notification::make()
                    ->title(__('inventory.import_successful'))
                    ->body($body)
                    ->success()
                    ->send();
            }

            return redirect()->back();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title(__('inventory.import_failed'))
                ->body(__('inventory.import_error_occurred', ['message' => $e->getMessage()]))
                ->danger()
                ->send();
                
            return redirect()->back();
        }
    }

    public function detail($id)
    {
        $item = Item::with(['supplier', 'unit', 'productType'])->findOrFail($id);

        $stockIns = \App\Models\StockIn::where('item_id', $item->id)
            ->with('user')
            ->orderBy('trx_date', 'desc')
            ->get();

        $stockOuts = \App\Models\StockOut::where('item_id', $item->id)
            ->with('user')
            ->orderBy('trx_date', 'desc')
            ->get();

        return view('inventory.detail', compact('item', 'stockIns', 'stockOuts'));
    }
}
