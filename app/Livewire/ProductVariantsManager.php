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

    // Modal state
    public bool $isModalOpen = false;
    public ?int $editingVariantId = null;

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
        $url = request()->url();
        $this->isReadOnly = str_contains($url, '/view') || !str_contains($url, '/edit');
        
        $this->productTypes = ProductType::orderBy('name')->get();
        $this->sizeOptions = SizeOption::where('status', 'active')->ordered()->get();
        
        $this->loadVariants();
    }

    public function loadVariants()
    {
        if ($this->product) {
            $this->variants = Variant::with(['productType', 'stocks.sizeOption'])
                ->where('product_id', $this->product->id)
                ->get();
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
        $this->color = '#4F46E5';
        $this->imageFile = null;
        $this->existingImage = null;
        // Pre-select all active size options by default
        $this->selectedSizes = $this->sizeOptions->pluck('id')->map(fn($id) => (string)$id)->toArray();
        
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
        $this->color = $variant->color ?? '#4F46E5';
        $this->imageFile = null;
        $this->existingImage = $variant->image;
        
        $this->selectedSizes = $variant->stocks->pluck('size_option_id')->map(fn($id) => (string)$id)->toArray();
        
        $this->isModalOpen = true;
    }

    public function saveVariant()
    {
        if ($this->isReadOnly) return;

        $this->validate([
            'variantName' => 'required|string|max:255',
            'productTypeId' => 'required|exists:master_product_type,id',
            'color' => 'required|string|max:7',
            'variantCode' => 'nullable|string|max:100',
            'imageFile' => 'nullable|image|max:2048', // 2MB max
            'selectedSizes' => 'required|array|min:1',
            'selectedSizes.*' => 'exists:size_options,id',
        ], [
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

    public function deleteVariant($variantId)
    {
        if ($this->isReadOnly) return;

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
        return view('livewire.product-variants-manager');
    }
}
