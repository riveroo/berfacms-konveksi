<x-filament-panels::layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <x-text variant="title">Inventory Overview</x-text>
                <x-text variant="muted" class="mt-1">Items inventory report and stock status</x-text>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <x-button href="{{ route('inventory.overview.export', request()->all()) }}" variant="outline" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export Excel
                </x-button>

                <div x-data="{ isModalOpen: false }">
                    <x-button type="button" @click="isModalOpen = true" variant="outline" class="w-full sm:w-auto">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import Stock
                    </x-button>

                    <!-- Import Modal -->
                    <div x-cloak x-show="isModalOpen"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div @click.away="isModalOpen = false"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md p-6">

                            <form action="{{ route('inventory.overview.import') }}" method="POST" enctype="multipart/form-data"
                                class="space-y-4">
                                @csrf

                                <!-- Title -->
                                <x-text variant="heading">Import Stock</x-text>

                                <!-- Template Download -->
                                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3 rounded-lg mb-4 flex justify-between items-center">
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        Need the format? <br>Columns: <span class="font-bold">item_code</span>, <span class="font-bold">stock_qty</span>
                                    </div>
                                    <x-button href="{{ route('inventory.overview.template') }}" variant="outline" class="h-8 px-3 text-xs">
                                        Download Template
                                    </x-button>
                                </div>

                                <!-- Description -->
                                <x-text variant="muted" class="mt-1">
                                    Note: Stock value will be SET to the provided <b>stock_qty</b>.
                                </x-text>

                                <!-- File Input -->
                                <div class="mt-4">
                                    <input type="file" name="file" accept=".xlsx,.xls" required
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                                </div>

                                <!-- Actions -->
                                <div
                                    class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                                    <x-button type="button" @click="isModalOpen = false" variant="outline">
                                        Cancel
                                    </x-button>
                                    <x-button type="submit" @click="setTimeout(() => isModalOpen = false, 150)" variant="primary">
                                        Upload & Import
                                    </x-button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('inventory.overview') }}"
            class="flex flex-wrap items-end gap-4 mb-6 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            
            <!-- Filter Supplier -->
            <div class="w-full sm:w-48">
                <x-text variant="label" class="mb-1.5 ml-1 text-[10px]">Supplier</x-text>
                <select name="supplier_id" onchange="this.form.submit()"
                    class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Product Type -->
            <div class="w-full sm:w-48">
                <x-text variant="label" class="mb-1.5 ml-1 text-[10px]">Product Type</x-text>
                <select name="product_type_id" onchange="this.form.submit()"
                    class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                    <option value="">All Product Types</option>
                    @foreach($productTypes as $type)
                        <option value="{{ $type->id }}" {{ request('product_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Input -->
            <div class="flex-1 min-w-[240px]">
                <x-text variant="label" class="mb-1.5 ml-1 text-[10px]">Search Item</x-text>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search item name or code..."
                        class="w-full h-10 pl-3 pr-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="indigo" class="h-10 px-5">
                    Search
                </x-button>
                @if(request('search') || request('supplier_id') || request('product_type_id'))
                    <x-button href="{{ route('inventory.overview') }}" variant="outline" class="h-10 px-4">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>

        <div
            class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                    <thead
                        class="bg-gray-50 dark:bg-gray-800/50 uppercase text-[10px] text-gray-500 font-bold tracking-wider sticky top-0 z-10 shadow-sm shadow-gray-200/50 dark:shadow-gray-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-gray-50 dark:bg-gray-800/50">Item ID / Code</th>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-gray-50 dark:bg-gray-800/50">Item Name</th>
                            <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-gray-50 dark:bg-gray-800/50">Details</th>
                            <th scope="col" class="px-6 py-4 text-right whitespace-nowrap bg-gray-50 dark:bg-gray-800/50">Current Stock</th>
                            <th scope="col" class="px-6 py-4 text-right whitespace-nowrap bg-gray-50 dark:bg-gray-800/50 border-r border-gray-200 dark:border-gray-700">Min. Stock</th>
                            <th scope="col" class="px-6 py-4 text-right whitespace-nowrap bg-gray-50 dark:bg-gray-800/50">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @foreach($items as $item)
                            <tr class="hover:bg-indigo-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-gray-100">
                                    <div class="font-bold">{{ $item->item_code }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $item->item_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $item->item_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    <div class="text-[11px] text-gray-500">Type: <span class="font-semibold">{{ optional($item->productType)->name ?? '-' }}</span></div>
                                    <div class="text-[11px] text-gray-500">Unit: <span class="font-semibold">{{ optional($item->unit)->name ?? '-' }}</span></div>
                                    <div class="text-[11px] text-gray-500">Supplier: <span class="font-semibold">{{ optional($item->supplier)->name ?? '-' }}</span></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-500 dark:text-gray-400">
                                    {{ $item->stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500 dark:text-gray-400 border-r border-gray-200 dark:border-gray-800">
                                    {{ $item->minimum_stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        @if($items->isEmpty())
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/20">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    No items found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::layout>
