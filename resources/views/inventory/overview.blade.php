<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Inventory Overview</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Items inventory report and stock status</p>
                </div>
                @if (canAccessMenu('admin/import-export'))
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <x-button href="{{ route('inventory.overview.export', request()->all()) }}" variant="outline"
                        class="w-full sm:w-auto">
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
                                <form action="{{ route('inventory.overview.import') }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Import Stock</h3>
                                    <div
                                        class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3 rounded-lg mb-4 flex justify-between items-center">
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            Need the format? <br>Columns: <span class="font-bold">ITEM-ID</span>,
                                            <span class="font-bold">Item Name</span>, <span
                                                class="font-bold">Stock</span>
                                        </div>
                                        <x-button href="{{ route('inventory.overview.template') }}" variant="outline"
                                            class="h-8 px-3 text-xs">
                                            Download Template
                                        </x-button>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Note: Stock value will be SET to the provided <b>stock_qty</b>.
                                    </p>
                                    <div class="mt-4">
                                        <input type="file" name="file" accept=".xlsx,.xls" required
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                                    </div>
                                    <div
                                        class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                                        <x-button type="button" @click="isModalOpen = false"
                                            variant="outline">Cancel</x-button>
                                        <x-button type="submit" @click="setTimeout(() => isModalOpen = false, 150)"
                                            variant="primary">Upload & Import</x-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <form method="GET" action="{{ route('inventory.overview') }}"
                class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end w-full">
                    <!-- Supplier Filter -->
                    <div class="w-full">
                        <label
                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 ml-1">Supplier</label>
                        <select name="supplier_id"
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Type Filter -->
                    <div class="w-full">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 ml-1">Product
                            Type</label>
                        <select name="product_type_id"
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                            <option value="">All Product Types</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type->id }}" {{ request('product_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="w-full">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 ml-1">Search
                            Item</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search item name or code..."
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 w-full">
                        <button type="submit"
                            class="flex-1 h-10 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Search
                        </button>
                        <a href="{{ route('inventory.overview') }}"
                            class="flex-1 h-10 px-4 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div
                class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col"
                                    class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    No</th>
                                <th scope="col"
                                    class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Item ID</th>
                                <th scope="col"
                                    class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Item Code</th>
                                <th scope="col"
                                    class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Item Name</th>
                                <th scope="col"
                                    class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Item Type</th>
                                <th scope="col"
                                    class="px-3 py-2 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Stock</th>
                                <th scope="col"
                                    class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Units</th>
                                <th scope="col"
                                    class="px-3 py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse($items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-sm">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        {{ $items->firstItem() + $index }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        {{ $item->item_id }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap font-bold text-gray-900 dark:text-gray-100">
                                        {{ $item->item_code }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $item->item_name }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ optional($item->productType)->name ?? '-' }}
                                    </td>
                                    <td
                                        class="px-3 py-2 whitespace-nowrap text-right font-bold text-gray-900 dark:text-gray-100">
                                        {{ $item->stock }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ optional($item->unit)->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center">
                                         <a href="{{ route('inventory.overview.detail', $item->id) }}" target="_blank"
                                             class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-indigo-600 dark:border-gray-700 dark:bg-gray-800 dark:text-indigo-400 dark:hover:bg-gray-700 transition-colors shadow-sm">
                                             <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                             </svg>
                                             Details
                                         </a>
                                     </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8"
                                        class="px-3 py-12 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/20">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        No items found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <!-- Show Per Page -->
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Show</span>
                            <form method="GET" action="{{ route('inventory.overview') }}" id="perPageForm">
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
                            <span class="text-xs text-gray-500 dark:text-gray-400">per page</span>
                        </div>

                        <!-- Pagination -->
                        <div class="pagination-container">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Fix pagination layout and spacing */
        .pagination-container nav {
            @apply flex flex-col sm:flex-row items-center gap-4;
        }

        /* Desktop view container in Laravel Pagination */
        .pagination-container nav>div:last-child {
            @apply flex flex-col sm:flex-row items-center gap-6 !important;
        }

        /* The 'Showing results' div */
        .pagination-container nav>div:last-child>div:first-child {
            @apply mb-0 mr-4 text-xs text-gray-600 dark:text-gray-400 !important;
        }

        /* The buttons div */
        .pagination-container nav>div:last-child>div:last-child {
            @apply flex items-center space-x-1 !important;
        }

        .pagination-container nav span[aria-current="page"]>span {
            @apply bg-indigo-600 text-white border-indigo-600 !important;
        }

        .pagination-container nav a,
        .pagination-container nav span {
            @apply px-1 py-1 text-xs rounded-md border border-gray-300 dark:border-gray-700 transition-colors !important;
        }
    </style>
</x-filament-panels::layout>