<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Dynamic Rows Table --}}
        <div class="p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/40 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-800">
                            <th class="px-4 py-3.5 w-12 text-center">#</th>
                            <th class="px-4 py-3.5 w-48">{{ __('stock.item_type') }}</th>
                            <th class="px-4 py-3.5">{{ __('stock.item_name') }}</th>
                            <th class="px-4 py-3.5 w-48">{{ __('stock.variant') }}</th>
                            <th class="px-4 py-3.5 w-40">{{ __('stock.size_option') }}</th>
                            <th class="px-4 py-3.5 w-32">{{ __('stock.quantity') }}</th>
                            <th class="px-4 py-3.5 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($rows as $index => $row)
                            <tr wire:key="row-{{ $index }}" class="hover:bg-gray-50/30 dark:hover:bg-gray-800/10 transition">
                                <td class="px-4 py-3 text-center text-sm font-medium text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                
                                {{-- Item Type --}}
                                <td class="px-4 py-3">
                                    <select 
                                        wire:model.live="rows.{{ $index }}.item_type"
                                        wire:change="onItemTypeChange({{ $index }})"
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white text-sm"
                                        required
                                    >
                                        <option value="product">{{ __('stock.product') }}</option>
                                        <option value="material">{{ __('stock.material') }}</option>
                                    </select>
                                </td>

                                {{-- Item/Product Selection --}}
                                <td class="px-4 py-3">
                                    @if ($row['item_type'] === 'product')
                                        <select 
                                            wire:model.live="rows.{{ $index }}.product_id"
                                            wire:change="onProductChange({{ $index }})"
                                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white text-sm"
                                            required
                                        >
                                            <option value="">-- {{ __('stock.select_product') }} --</option>
                                            @foreach ($this->getProducts() as $product)
                                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                            @endforeach
                                        </select>
                                        @error("rows.{$index}.product_id")
                                            <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <select 
                                            wire:model.live="rows.{{ $index }}.item_id"
                                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white text-sm"
                                            required
                                        >
                                            <option value="">-- {{ __('stock.select_material') }} --</option>
                                            @foreach ($this->getMaterials() as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_name }} ({{ $item->item_code }})</option>
                                            @endforeach
                                        </select>
                                        @error("rows.{$index}.item_id")
                                            <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    @endif
                                </td>

                                {{-- Variant --}}
                                <td class="px-4 py-3">
                                    @if ($row['item_type'] === 'product')
                                        <select 
                                            wire:model.live="rows.{{ $index }}.variant_id"
                                            wire:change="onVariantChange({{ $index }})"
                                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white text-sm"
                                            @disabled(empty($row['variants']))
                                            required
                                        >
                                            <option value="">-- {{ __('stock.select_variant') }} --</option>
                                            @foreach ($row['variants'] as $variant)
                                                <option value="{{ $variant['id'] }}">{{ $variant['variant_name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error("rows.{$index}.variant_id")
                                            <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-600 font-medium">-</span>
                                    @endif
                                </td>

                                {{-- Size Option --}}
                                <td class="px-4 py-3">
                                    @if ($row['item_type'] === 'product')
                                        <select 
                                            wire:model.live="rows.{{ $index }}.size_option_id"
                                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white text-sm"
                                            @disabled(empty($row['sizes']))
                                        >
                                            <option value="">-- {{ __('stock.select_size') }} --</option>
                                            @foreach ($row['sizes'] as $size)
                                                <option value="{{ $size['id'] }}">{{ $size['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @error("rows.{$index}.size_option_id")
                                            <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-600 font-medium">-</span>
                                    @endif
                                </td>

                                {{-- Quantity --}}
                                <td class="px-4 py-3">
                                    <input 
                                        type="number" 
                                        wire:model.live="rows.{{ $index }}.quantity"
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-950 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white text-sm"
                                        min="1"
                                        required
                                    >
                                    @error("rows.{$index}.quantity")
                                        <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">{{ $message }}</p>
                                    @enderror
                                </td>

                                {{-- Delete Button --}}
                                <td class="px-4 py-3 text-center">
                                    <button 
                                        type="button"
                                        wire:click="removeRow({{ $index }})"
                                        class="text-danger-600 dark:text-danger-400 hover:text-danger-500 transition-colors p-1"
                                        title="{{ __('stock.delete_row') }}"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-start">
                <button 
                    type="button" 
                    wire:click="addRow" 
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 border border-transparent rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('stock.add_row') }}
                </button>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3">
            <button 
                type="submit" 
                @disabled(! $this->isFormValid())
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary-600 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg hover:bg-primary-500 transition shadow-sm"
            >
                {{ __('stock.save') }}
            </button>
            
            <a 
                href="/admin/stock-in" 
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm"
            >
                {{ __('stock.cancel') }}
            </a>
        </div>
    </form>
</x-filament-panels::page>
