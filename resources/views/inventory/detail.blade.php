<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">
            <!-- Header section with back button -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('inventory.overview') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Overview
                        </a>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white flex items-center gap-2">
                        <span>Item Details:</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-extrabold">{{ $item->item_name }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detailed information and transaction logs for this item</p>
                </div>
            </div>

            <!-- Item Information Section -->
            <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Item Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Item ID -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Item ID</span>
                            <span class="text-sm font-mono font-bold text-gray-900 dark:text-gray-100">{{ $item->item_id }}</span>
                        </div>

                        <!-- Item Code -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Item Code</span>
                            <span class="text-sm font-mono font-bold text-gray-900 dark:text-gray-100">{{ $item->item_code }}</span>
                        </div>

                        <!-- Item Type -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Item Type</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ optional($item->productType)->name ?? '-' }}</span>
                        </div>

                        <!-- Unit -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Unit</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ optional($item->unit)->name ?? '-' }}</span>
                        </div>

                        <!-- Supplier -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Supplier</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ optional($item->supplier)->name ?? '-' }}</span>
                        </div>

                        <!-- Minimum Stock -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Minimum Stock</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $item->minimum_stock }}</span>
                        </div>

                        <!-- Price -->
                        <div class="bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-1">Price</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>

                        <!-- Current Stock -->
                        <div class="bg-indigo-50/50 dark:bg-indigo-950/20 p-4 rounded-xl border border-indigo-100/50 dark:border-indigo-900/30">
                            <span class="text-xs font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider block mb-1">Current Stock</span>
                            <span class="text-lg font-black text-indigo-700 dark:text-indigo-300">{{ $item->stock }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock In & Stock Out section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Stock In Column -->
                <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-green-50/20 dark:bg-green-950/10 flex justify-between items-center">
                        <h3 class="text-base font-bold text-green-700 dark:text-green-400 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Stock In History
                        </h3>
                        <span class="text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 px-2.5 py-0.5 rounded-full">
                            {{ $stockIns->count() }} records
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trx Date</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Update Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                @forelse($stockIns as $in)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-sm">
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300 font-mono">
                                            {{ $in->trx_date->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400 font-medium">
                                            {{ optional($in->user)->name ?? 'System' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-green-600 dark:text-green-400">
                                            +{{ $in->quantity }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No stock in records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Stock Out Column -->
                <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-red-50/20 dark:bg-red-950/10 flex justify-between items-center">
                        <h3 class="text-base font-bold text-red-700 dark:text-red-400 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                            Stock Out History
                        </h3>
                        <span class="text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 px-2.5 py-0.5 rounded-full">
                            {{ $stockOuts->count() }} records
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trx Date</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Update Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                @forelse($stockOuts as $out)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-sm">
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300 font-mono">
                                            {{ $out->trx_date->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400 font-medium">
                                            {{ optional($out->user)->name ?? 'System' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-red-600 dark:text-red-400">
                                            -{{ $out->quantity }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No stock out records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::layout>
