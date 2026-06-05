<x-filament-panels::layout>
    <div class="space-y-6" x-data="{ 
        isEditModalOpen: false,
        isImportModalOpen: false,
        activeStockId: null,
        activeProductName: '',
        activeVariantName: '',
        activeSizeName: '',
        activeCogs: 0,
        activePrice: 0,

        openEditModal(id, productName, variantName, sizeName, cogs, price) {
            this.activeStockId = id;
            this.activeProductName = productName;
            this.activeVariantName = variantName;
            this.activeSizeName = sizeName;
            this.activeCogs = Math.floor(parseFloat(cogs) || 0);
            this.activePrice = Math.floor(parseFloat(price) || 0);
            this.isEditModalOpen = true;
        }
    }">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <x-text variant="title">{{ __('product.product_pricing') }}</x-text>
                <x-text variant="muted" class="mt-1">{{ __('product.product_pricing_desc') }}</x-text>
            </div>
            
            @if (canAccessMenu('admin/import-export'))
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <!-- Export Template -->
                <x-button href="{{ route('admin.product-pricing.export') }}" variant="outline" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('product.download_template') }}
                </x-button>

                <!-- Import Button -->
                <x-button type="button" @click="isImportModalOpen = true" variant="indigo" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    {{ __('product.import_pricing') }}
                </x-button>
            </div>
            @endif
        </div>

        <!-- Filters Section -->
        <form method="GET" action="{{ route('admin.product-pricing') }}"
            class="flex flex-wrap items-end gap-4 mb-6 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <!-- Filter Product -->
            <div class="w-full sm:w-64">
                <x-text variant="label" class="mb-1.5 ml-1 text-[10px]">{{ __('product.filter_product') }}</x-text>
                <select name="product_id" onchange="this.form.submit()"
                    class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                    <option value="">{{ __('product.all_products') }}</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Input -->
            <div class="flex-1 min-w-[240px]">
                <x-text variant="label" class="mb-1.5 ml-1 text-[10px]">{{ __('product.search_product_variant') }}</x-text>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('product.search_name_code') }}"
                    class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="indigo" class="h-10 px-5">
                    {{ __('product.apply_filter') }}
                </x-button>
                @if(request('search') || request('product_id'))
                    <x-button href="{{ route('admin.product-pricing') }}" variant="outline" class="h-10 px-4">
                        {{ __('product.reset') }}
                    </x-button>
                @endif
            </div>
        </form>

        <!-- Pricing Records Table -->
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 uppercase text-[10px] text-gray-500 font-bold tracking-wider sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap">{{ __('product.stock_id') }}</th>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap">{{ __('product.product_name') }}</th>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap">{{ __('product.variant_name') }}</th>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap">{{ __('product.color') }}</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">{{ __('product.ukuran') }}</th>
                            <th scope="col" class="px-6 py-4 text-right whitespace-nowrap">{{ __('product.hpp') }}</th>
                            <th scope="col" class="px-6 py-4 text-right whitespace-nowrap">{{ __('product.harga_jual') }}</th>
                            <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">{{ __('product.aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @forelse($stocks as $stock)
                            <tr class="hover:bg-indigo-50/50 dark:hover:bg-white/5 transition-colors">
                                <!-- ID -->
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-semibold text-gray-500">
                                    #{{ $stock->id }}
                                </td>
                                
                                <!-- Product Name -->
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100 font-medium">
                                    {{ optional(optional($stock->variant)->product)->product_name }}
                                </td>
                                
                                <!-- Variant Name -->
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300 font-medium">
                                    {{ optional($stock->variant)->variant_name }}
                                </td>
                                
                                <!-- Color -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-4 rounded-full border border-gray-300 dark:border-gray-650 shadow-sm" style="background-color: {{ optional($stock->variant)->color }};"></span>
                                        <span class="text-xs font-mono text-gray-500">{{ strtoupper(optional($stock->variant)->color) }}</span>
                                    </div>
                                </td>

                                <!-- Size -->
                                <td class="px-6 py-4 text-center whitespace-nowrap font-bold text-indigo-650 dark:text-indigo-400">
                                    {{ optional($stock->sizeOption)->name }}
                                </td>

                                <!-- COGS -->
                                <td class="px-6 py-4 text-right whitespace-nowrap font-bold font-mono text-slate-700 dark:text-slate-350">
                                    Rp {{ number_format($stock->cogs, 0, ',', '.') }}
                                </td>

                                <!-- Selling Price -->
                                <td class="px-6 py-4 text-right whitespace-nowrap font-bold font-mono text-indigo-600 dark:text-indigo-400">
                                    Rp {{ number_format($stock->price, 0, ',', '.') }}
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <x-button type="button" variant="outline" size="sm" class="h-8"
                                        @click="openEditModal('{{ $stock->id }}', '{{ addslashes(optional(optional($stock->variant)->product)->product_name) }}', '{{ addslashes(optional($stock->variant)->variant_name) }}', '{{ optional($stock->sizeOption)->name }}', '{{ $stock->cogs }}', '{{ $stock->price }}')">
                                        <svg class="w-4 h-4 text-indigo-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        {{ __('product.ubah_harga') }}
                                    </x-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500 bg-gray-50/50 dark:bg-gray-800/10">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h.007m-.007 3h.007m-.007 3h.007m-2.25 3h18.75m-18.75 3h.008M2.25 12h19.5M2.25 9h19.5M2.25 6h19.5" />
                                        </svg>
                                        <p class="font-semibold text-sm">{{ __('product.no_pricing_records') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer (Pagination & Items per page) -->
            <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <!-- Items Per Page -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('product.show') }}</span>
                        <form method="GET" action="{{ route('admin.product-pricing') }}" id="perPageForm">
                            @foreach(request()->except('perPage') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="perPage" onchange="document.getElementById('perPageForm').submit()"
                                class="h-8 px-2 w-12 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs text-gray-700 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500 outline-none cursor-pointer">
                                <option value="10" {{ request('perPage') == '10' ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('perPage') == '25' ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('perPage') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('perPage') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('product.per_page') }}</span>
                    </div>

                    <!-- Pagination Links -->
                    <div class="pagination-container">
                        {{ $stocks->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Single Edit Pricing Modal Overlay -->
        <div x-cloak x-show="isEditModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition duration-300"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
            
            <div @click.away="isEditModalOpen = false" 
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-6 relative">
                
                <button type="button" @click="isEditModalOpen = false" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div>
                    <x-text variant="title" class="text-xl font-bold">{{ __('product.ubah_harga') }}</x-text>
                    <x-text variant="muted" class="text-xs mt-1">{{ __('product.edit_pricing_desc') }}</x-text>
                </div>

                <form :action="'{{ route('admin.product-pricing.update', '__ID__') }}'.replace('__ID__', activeStockId)" method="POST" class="space-y-4">
                    @csrf

                    <!-- Meta details (Read-only) -->
                    <div class="grid grid-cols-2 gap-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800/50 text-xs">
                        <div>
                            <span class="text-gray-400 block uppercase font-bold tracking-wider">{{ __('product.product_variant') }}</span>
                            <span class="font-bold text-gray-700 dark:text-gray-200" x-text="activeProductName + ' - ' + activeVariantName"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block uppercase font-bold tracking-wider">{{ __('product.ukuran') }}</span>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="activeSizeName"></span>
                        </div>
                    </div>

                    <!-- COGS Input -->
                    <div>
                        <x-text variant="label" class="mb-1.5">{{ __('product.hpp') }} <span class="text-red-500">*</span></x-text>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">Rp</span>
                            <input type="number" name="cogs" x-model.number="activeCogs" @input="activeCogs = activeCogs ? parseInt(activeCogs) : 0" min="0" required
                                class="w-full h-10 pl-9 pr-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150 font-mono font-bold">
                        </div>
                    </div>

                    <!-- Selling Price Input -->
                    <div>
                        <x-text variant="label" class="mb-1.5">{{ __('product.harga_jual') }} <span class="text-red-500">*</span></x-text>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">Rp</span>
                            <input type="number" name="price" x-model.number="activePrice" @input="activePrice = activePrice ? parseInt(activePrice) : 0" min="0" required
                                class="w-full h-10 pl-9 pr-3 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150 font-mono font-bold">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-850 mt-4">
                        <x-button type="button" @click="isEditModalOpen = false" variant="outline">
                            {{ __('product.batal') }}
                        </x-button>
                        <x-button type="submit" variant="indigo">
                            {{ __('product.simpan_perubahan') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Import Modal Overlay -->
        <div x-cloak x-show="isImportModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition duration-300"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
            
            <div @click.away="isImportModalOpen = false" 
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-6 relative">
                
                <button type="button" @click="isImportModalOpen = false" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div>
                    <x-text variant="title" class="text-xl font-bold">{{ __('product.impor_massal_harga') }}</x-text>
                    <x-text variant="muted" class="text-xs mt-1">{{ __('product.impor_massal_desc') }}</x-text>
                </div>

                <form action="{{ route('admin.product-pricing.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Instructions / Warnings -->
                    <div class="bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/50 p-4 rounded-xl text-xs space-y-2">
                        <span class="block font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wide">{{ __('product.panduan_impor') }}</span>
                        <ul class="list-disc pl-4 text-gray-600 dark:text-gray-400 space-y-1">
                            <li>{{ __('product.panduan_1') }}</li>
                            <li>{{ __('product.panduan_2') }}</li>
                            <li>{{ __('product.panduan_3') }}</li>
                        </ul>
                    </div>

                    <!-- File input -->
                    <div>
                        <x-text variant="label" class="mb-1.5">{{ __('product.unggah_file') }}</x-text>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 bg-gray-50 dark:bg-gray-900 text-gray-750 dark:text-gray-300 text-sm">
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-850 mt-4">
                        <x-button type="button" @click="isImportModalOpen = false" variant="outline">
                            {{ __('product.batal') }}
                        </x-button>
                        <x-button type="submit" variant="indigo">
                            {{ __('product.unggah_dan_impor') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Fix pagination layout and spacing */
        .pagination-container nav {
            @apply flex flex-col sm:flex-row items-center gap-4;
        }
        .pagination-container nav>div:last-child {
            @apply flex flex-col sm:flex-row items-center gap-6 !important;
        }
        .pagination-container nav>div:last-child>div:first-child {
            @apply mb-0 mr-4 text-xs text-gray-600 dark:text-gray-400 !important;
        }
        .pagination-container nav>div:last-child>div:last-child {
            @apply flex items-center space-x-1 !important;
        }
        .pagination-container nav span[aria-current="page"]>span {
            @apply bg-indigo-600 text-white border-indigo-600 !important;
        }
        .pagination-container nav a,
        .pagination-container nav span {
            @apply px-1.5 py-1 text-xs rounded-md border border-gray-300 dark:border-gray-700 transition-colors !important;
        }
    </style>
</x-filament-panels::layout>
