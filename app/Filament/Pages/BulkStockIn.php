<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class BulkStockIn extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-on-rectangle';
    protected static ?string $slug = 'stock-in/bulk';
    protected static string $view = 'admin.stock-in.bulk';
    protected static bool $shouldRegisterNavigation = false;

    public $rows = [];

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/stock-in');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('stock.bulk_stock_in');
    }

    public function mount()
    {
        $this->addRow();
    }

    public function addRow()
    {
        $this->rows[] = [
            'item_type' => 'product', // 'product' or 'material'
            'product_id' => '',
            'variant_id' => '',
            'size_option_id' => '',
            'item_id' => '',
            'quantity' => 1,
            'variants' => [],
            'sizes' => [],
        ];
    }

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        if (empty($this->rows)) {
            $this->addRow();
        }
    }

    public function onItemTypeChange($index)
    {
        $this->rows[$index]['product_id'] = '';
        $this->rows[$index]['variant_id'] = '';
        $this->rows[$index]['size_option_id'] = '';
        $this->rows[$index]['item_id'] = '';
        $this->rows[$index]['variants'] = [];
        $this->rows[$index]['sizes'] = [];
    }

    public function onProductChange($index)
    {
        $productId = $this->rows[$index]['product_id'];
        if ($productId) {
            $this->rows[$index]['variants'] = Variant::where('product_id', $productId)
                ->get()
                ->map(fn($v) => [
                    'id' => $v->id,
                    'variant_name' => $v->variant_name,
                ])
                ->toArray();
        } else {
            $this->rows[$index]['variants'] = [];
        }
        $this->rows[$index]['variant_id'] = '';
        $this->rows[$index]['size_option_id'] = '';
        $this->rows[$index]['sizes'] = [];
    }

    public function onVariantChange($index)
    {
        $variantId = $this->rows[$index]['variant_id'];
        if ($variantId) {
            $this->rows[$index]['sizes'] = Stock::where('variant_id', $variantId)
                ->whereNotNull('size_option_id')
                ->with('sizeOption')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->size_option_id,
                    'name' => $s->sizeOption ? $s->sizeOption->name : '-',
                ])
                ->toArray();
        } else {
            $this->rows[$index]['sizes'] = [];
        }
        $this->rows[$index]['size_option_id'] = '';
    }

    public function save()
    {
        $this->validate([
            'rows.*.item_type' => 'required|in:product,material',
            'rows.*.product_id' => 'required_if:rows.*.item_type,product',
            'rows.*.variant_id' => 'required_if:rows.*.item_type,product',
            'rows.*.size_option_id' => 'nullable',
            'rows.*.item_id' => 'required_if:rows.*.item_type,material',
            'rows.*.quantity' => 'required|integer|min:1',
        ], [
            'rows.*.product_id.required_if' => __('stock.product_required'),
            'rows.*.variant_id.required_if' => __('stock.variant_required'),
            'rows.*.item_id.required_if' => __('stock.material_required'),
            'rows.*.quantity.required' => __('stock.qty_required'),
            'rows.*.quantity.min' => __('stock.qty_min'),
        ]);

        try {
            DB::transaction(function () {
                $timezone = session('device_timezone') ?? config('app.timezone');
                $localTime = \Carbon\Carbon::now($timezone);
                $now = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $localTime->toDateTimeString(), config('app.timezone'));
                
                foreach ($this->rows as $row) {
                    if ($row['item_type'] === 'product') {
                        StockIn::create([
                            'trx_date' => $now,
                            'item_type' => 'product',
                            'product_id' => $row['product_id'],
                            'variant_id' => $row['variant_id'],
                            'size_option_id' => $row['size_option_id'] ?: null,
                            'quantity' => $row['quantity'],
                            'user_id' => auth()->id(),
                        ]);
                    } else {
                        StockIn::create([
                            'trx_date' => $now,
                            'item_type' => 'material',
                            'item_id' => $row['item_id'],
                            'quantity' => $row['quantity'],
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            });

            Notification::make()
                ->title(__('stock.bulk_stock_in_success'))
                ->success()
                ->send();

            return redirect()->to('/admin/stock-in');
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('stock.bulk_stock_in_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getProducts()
    {
        return Product::where('is_active', true)
            ->where(fn($q) => $q->whereNull('is_service')->orWhere('is_service', '!=', 'yes'))
            ->orderBy('product_name')
            ->get();
    }

    public function getMaterials()
    {
        return Item::orderBy('item_name')->get();
    }

    public function isFormValid(): bool
    {
        if (empty($this->rows)) {
            return false;
        }

        foreach ($this->rows as $row) {
            if (empty($row['item_type'])) {
                return false;
            }
            if ($row['item_type'] === 'product') {
                if (empty($row['product_id']) || empty($row['variant_id'])) {
                    return false;
                }
            } elseif ($row['item_type'] === 'material') {
                if (empty($row['item_id'])) {
                    return false;
                }
            } else {
                return false;
            }
            if (empty($row['quantity']) || $row['quantity'] < 1) {
                return false;
            }
        }

        return true;
    }
}
