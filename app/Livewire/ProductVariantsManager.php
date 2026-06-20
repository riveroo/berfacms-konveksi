<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductType;
use App\Models\SizeOption;
use App\Models\Stock;
use Illuminate\Support\Facades\Storage;

class ProductVariantsManager extends Component
{
    use WithFileUploads;

    public ?Product $product = null;
    public bool $isReadOnly = false;

    // Table state
    public $variants = [];
    public $search = '';

    // Modal state
    public ?int $editingVariantId = null;
    public bool $isModalOpen = false;
    
    // Error modal state for delete validation
    public bool $isErrorModalOpen = false;
    public string $errorMessage = '';
    public array $variantUsages = [];

    // Form fields
    public $variantCode = '';
    public $variantName = '';
    public $productTypeId = '';
    public $color = '#4F46E5';
    public $imageFile;
    public $existingImage = null;
    public array $selectedSizes = []; // Array of size_option_ids

    // Loaded master data
    public $productTypes = [];
    public $sizeOptions = [];

    protected $listeners = ['refreshVariants' => 'loadVariants'];

    public function mount(?Product $record = null)
    {
        $this->product = $record;
        
        // Safely determine read-only mode by route name or URL structure
        if (app()->runningUnitTests()) {
            $this->isReadOnly = false;
        } else {
            $url = request()->url();
            $this->isReadOnly = str_contains($url, '/view') || !str_contains($url, '/edit');
        }
        
        $this->productTypes = ProductType::orderBy('name')->get();
        $this->sizeOptions = SizeOption::where('status', 'active')->ordered()->get();
        
        $this->loadVariants();
    }

    public function loadVariants()
    {
        if ($this->product) {
            $query = Variant::with(['productType', 'stocks.sizeOption'])
                ->where('product_id', $this->product->id);

            if (filled($this->search)) {
                $query->where('variant_name', 'like', '%' . $this->search . '%');
            }

            $this->variants = $query->get();
        } else {
            $this->variants = [];
        }
    }

    public function openAddModal()
    {
        if ($this->isReadOnly) return;

        $this->resetErrorBag();
        $this->editingVariantId = null;
        $this->variantCode = '';
        $this->variantName = '';
        $this->productTypeId = $this->productTypes->first()?->id ?? '';
        $this->imageFile = null;
        $this->existingImage = null;
        
        $isService = $this->product && $this->product->is_service === 'yes';
        if ($isService) {
            $this->color = '#FFFFFF';
            $this->selectedSizes = [];
        } else {
            $this->color = '#4F46E5';
            // Pre-select all active size options by default
            $this->selectedSizes = $this->sizeOptions->pluck('id')->map(fn($id) => (string)$id)->toArray();
        }
        
        $this->isModalOpen = true;
    }

    public function openEditModal($variantId)
    {
        if ($this->isReadOnly) return;

        $this->resetErrorBag();
        $this->editingVariantId = $variantId;
        
        $variant = Variant::with('stocks')->findOrFail($variantId);
        
        $this->variantCode = $variant->variant_code;
        $this->variantName = $variant->variant_name;
        $this->productTypeId = $variant->product_type_id;
        $this->imageFile = null;
        $this->existingImage = $variant->image;
        
        $isService = $this->product && $this->product->is_service === 'yes';
        if ($isService) {
            $this->color = '#FFFFFF';
            $this->selectedSizes = [];
        } else {
            $this->color = $variant->color ?? '#4F46E5';
            $this->selectedSizes = $variant->stocks->pluck('size_option_id')->map(fn($id) => (string)$id)->toArray();
        }
        
        $this->isModalOpen = true;
    }

    public function saveVariant()
    {
        if ($this->isReadOnly) return;

        $isService = $this->product && $this->product->is_service === 'yes';

        $rules = [
            'variantName' => 'required|string|max:255',
            'productTypeId' => 'required|exists:master_product_type,id',
            'variantCode' => 'nullable|string|max:100',
            'imageFile' => 'nullable|image|max:2048', // 2MB max
        ];

        if ($isService) {
            $this->color = '#FFFFFF';
            $this->selectedSizes = [];
        } else {
            $rules['color'] = 'required|string|max:7';
            $rules['selectedSizes'] = 'required|array|min:1';
            $rules['selectedSizes.*'] = 'exists:size_options,id';
        }

        $this->validate($rules, [
            'variantName.required' => 'Variant name is required.',
            'selectedSizes.required' => 'At least one size option must be selected.',
            'selectedSizes.min' => 'At least one size option must be selected.',
        ]);

        \DB::transaction(function () {
            $imagePath = $this->existingImage;

            if ($this->imageFile) {
                // Delete existing image if any
                if ($this->existingImage) {
                    Storage::disk('public')->delete($this->existingImage);
                }
                $imagePath = $this->imageFile->store('variant-images', 'public');
            }

            $variantData = [
                'product_id' => $this->product->id,
                'variant_code' => $this->variantCode ?: 'VAR-' . strtoupper(uniqid()),
                'variant_name' => $this->variantName,
                'product_type_id' => $this->productTypeId,
                'color' => $this->color,
                'image' => $imagePath,
            ];

            if ($this->editingVariantId) {
                $variant = Variant::findOrFail($this->editingVariantId);
                $variant->update($variantData);
            } else {
                $variant = Variant::create($variantData);
            }

            // Sync stocks dynamically
            $currentSizeIds = $variant->stocks->pluck('size_option_id')->toArray();
            $newSizeIds = array_map('intval', $this->selectedSizes);

            // Delete sizes no longer selected
            $toDelete = array_diff($currentSizeIds, $newSizeIds);
            if (!empty($toDelete)) {
                $variant->stocks()->whereIn('size_option_id', $toDelete)->delete();
            }

            // Add new sizes with default values: stock = 0, price = 0
            $toAdd = array_diff($newSizeIds, $currentSizeIds);
            foreach ($toAdd as $sizeId) {
                Stock::create([
                    'variant_id' => $variant->id,
                    'size_option_id' => $sizeId,
                    'stock' => 0,
                    'price' => 0,
                ]);
            }
        });

        $this->isModalOpen = false;
        $this->loadVariants();

        \Filament\Notifications\Notification::make()
            ->title($this->editingVariantId ? 'Variant updated successfully!' : 'Variant created successfully!')
            ->success()
            ->send();
    }

    public function checkVariantUsage($variantId)
    {
        $usages = [];

        if (\DB::table('transaction_details')->where('variant_id', $variantId)->exists()) {
            $usages[] = 'Detail Transaksi (Sales / Orders)';
        }
        if (\DB::table('pre_order_details')->where('variant_id', $variantId)->exists()) {
            $usages[] = 'Detail Pre Order';
        }
        if (\DB::table('stock_ins')->where('variant_id', $variantId)->exists()) {
            $usages[] = 'Riwayat Stock In';
        }
        if (\DB::table('stock_outs')->where('variant_id', $variantId)->exists()) {
            $usages[] = 'Riwayat Stock Out';
        }
        if (\DB::table('stock_adjustments')->where('variant_id', $variantId)->exists()) {
            $usages[] = 'Riwayat Stock Adjustment';
        }
        if (\DB::table('production_products')->where('variant_id', $variantId)->exists()) {
            $usages[] = 'Riwayat Produksi';
        }

        return $usages;
    }

    public function deleteVariant($variantId)
    {
        if ($this->isReadOnly) return;

        // Check if the variant is in use
        $usages = $this->checkVariantUsage($variantId);

        if (!empty($usages)) {
            $variant = Variant::findOrFail($variantId);
            $msg = "Varian \"{$variant->variant_name}\" (Kode: {$variant->variant_code}) tidak dapat dihapus karena telah digunakan dalam riwayat transaksi atau modul sistem lainnya (" . implode(', ', $usages) . ").";
            $this->js("alert('" . addslashes($msg) . "')");
            return;
        }

        \DB::transaction(function () use ($variantId) {
            $variant = Variant::findOrFail($variantId);
            
            // Delete image from storage
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }

            // Cascade delete stocks and variant record
            $variant->stocks()->delete();
            $variant->delete();
        });

        $this->loadVariants();
        
        \Filament\Notifications\Notification::make()
            ->title('Variant and related stocks deleted successfully!')
            ->success()
            ->send();
    }

    public function render()
    {
        $this->loadVariants();
        return view('livewire.product-variants-manager');
    }
}
