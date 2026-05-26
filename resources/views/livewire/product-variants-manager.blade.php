<div>
    <!-- Variants Section Container -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col p-6 space-y-6">
        
        <!-- Header & Add Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div>
                <x-text variant="heading" class="text-gray-900 dark:text-white">Product Variants</x-text>
                <x-text variant="muted" class="mt-1">Manage styles, codes, product types, and size/stock combinations.</x-text>
            </div>
            
            @if (!$isReadOnly)
                <x-button type="button" wire:click="openAddModal" variant="indigo" size="md">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Variant
                </x-button>
            @endif
        </div>

        <!-- Variants Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-800">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs uppercase bg-gray-50/50 dark:bg-gray-800/40 text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-4">Image</th>
                        <th scope="col" class="px-6 py-4">Variant Code</th>
                        <th scope="col" class="px-6 py-4">Variant Name</th>
                        <th scope="col" class="px-6 py-4">Product Type</th>
                        <th scope="col" class="px-6 py-4">Color</th>
                        <th scope="col" class="px-6 py-4">Sizes</th>
                        @if (!$isReadOnly)
                            <th scope="col" class="px-6 py-4 text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($variants as $var)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-850/50 transition duration-150">
                            <!-- Image -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($var->image)
                                    <img src="{{ Storage::disk('public')->url($var->image) }}" alt="{{ $var->variant_name }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm transition hover:scale-105">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-dashed border-gray-200 dark:border-gray-700">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>

                            <!-- Code -->
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-white">
                                {{ $var->variant_code }}
                            </td>

                            <!-- Name -->
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ $var->variant_name }}
                            </td>

                            <!-- Product Type -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800 dark:bg-slate-850 dark:text-slate-200">
                                    {{ optional($var->productType)->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Color -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full border border-gray-300 dark:border-gray-600 shadow-sm" style="background-color: {{ $var->color }};"></span>
                                    <span class="text-xs font-mono text-gray-500">{{ strtoupper($var->color) }}</span>
                                </div>
                            </td>

                            <!-- Sizes -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @forelse($var->stocks as $stock)
                                        <span class="px-2 py-0.5 text-xs font-bold rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                            {{ optional($stock->sizeOption)->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No sizes active</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Actions -->
                            @if (!$isReadOnly)
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <x-button type="button" wire:click="openEditModal({{ $var->id }})" variant="outline" size="sm" class="h-8 px-2.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </x-button>
                                        
                                        <x-button type="button" wire:confirm="Are you sure you want to delete this variant and all its stocks?" wire:click="deleteVariant({{ $var->id }})" variant="danger" size="sm" class="h-8 px-2.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </x-button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isReadOnly ? 6 : 7 }}" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="h-12 w-12 text-gray-300 dark:text-gray-700 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm font-medium">No variants added yet.</p>
                                    @if (!$isReadOnly)
                                        <p class="text-xs text-gray-400">Click "Add Variant" to create the first style for this product.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Add / Edit Modal Overlay -->
        <div x-cloak x-data="{ open: @entangle('isModalOpen') }" x-show="open" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition duration-300"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
            
            <div @click.away="open = false" 
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col p-6 space-y-6 relative">
                
                <!-- Modal Close Button -->
                <button type="button" @click="open = false" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition duration-150 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Modal Title -->
                <div>
                    <x-text variant="title" class="text-xl font-bold">{{ $editingVariantId ? 'Edit Variant' : 'Add Variant' }}</x-text>
                    <x-text variant="muted" class="text-xs mt-1">Configure standard details and active sizes for this variant.</x-text>
                </div>

                <!-- Form Content -->
                <form wire:submit.prevent="saveVariant" class="space-y-5">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Variant Code -->
                        <div>
                            <x-text variant="label" class="mb-1.5">Variant Code</x-text>
                            <input type="text" wire:model="variantCode" placeholder="Auto-generated if empty"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                            @error('variantCode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Variant Name -->
                        <div>
                            <x-text variant="label" class="mb-1.5">Variant Name <span class="text-red-500">*</span></x-text>
                            <input type="text" wire:model="variantName" placeholder="e.g. Navy Blue"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                            @error('variantName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Product Type -->
                        <div>
                            <x-text variant="label" class="mb-1.5">Product Type <span class="text-red-500">*</span></x-text>
                            <select wire:model="productTypeId"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150 cursor-pointer">
                                <option value="">-- Select Product Type --</option>
                                @foreach($productTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('productTypeId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Color -->
                        <div>
                            <x-text variant="label" class="mb-1.5">Color Hex <span class="text-red-500">*</span></x-text>
                            <div class="flex items-center gap-3">
                                <input type="color" wire:model="color" class="w-10 h-10 rounded-lg cursor-pointer border border-gray-300 dark:border-gray-600 p-0.5 bg-white dark:bg-gray-900">
                                <input type="text" wire:model="color" placeholder="#4F46E5" maxlength="7"
                                    class="flex-1 h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150 font-mono">
                            </div>
                            @error('color') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <x-text variant="label" class="mb-1.5">Upload Image</x-text>
                        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 border border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50/50 dark:bg-gray-900/30">
                            @if ($imageFile)
                                <img src="{{ $imageFile->temporaryUrl() }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                            @elseif ($existingImage)
                                <img src="{{ Storage::disk('public')->url($existingImage) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700 text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1 w-full text-center sm:text-left">
                                <input type="file" wire:model="imageFile" accept="image/*" class="text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400 file:cursor-pointer hover:file:bg-indigo-100 transition duration-150">
                                <span class="block text-[10px] text-gray-400 dark:text-gray-500 mt-1">JPEG, PNG, WEBP (Max 2MB)</span>
                            </div>
                        </div>
                        @error('imageFile') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Size Checklist -->
                    <div>
                        <x-text variant="label" class="mb-3">Active Sizes <span class="text-red-500">*</span></x-text>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($sizeOptions as $size)
                                <label for="size_{{ $size->id }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition shadow-sm select-none">
                                    <input type="checkbox" id="size_{{ $size->id }}" value="{{ $size->id }}" wire:model="selectedSizes" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $size->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedSizes') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-5 border-t border-gray-100 dark:border-gray-800 mt-6">
                        <x-button type="button" @click="open = false" variant="outline">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="indigo">
                            {{ $editingVariantId ? 'Save Changes' : 'Create Variant' }}
                        </x-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
