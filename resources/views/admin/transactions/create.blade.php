<x-filament-panels::layout>
    <div x-data="pos()" class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <x-text variant="title">Create Transaction</x-text>
                <x-text variant="muted">Create direct POS transaction</x-text>
            </div>
            <x-button variant="outline" href="{{ route('transactions.index') }}">
                Back to Transactions
            </x-button>
        </div>

        <!-- Top Section: Client Information & Transaction Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Client Information -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <x-text variant="heading">Customer Information</x-text>
                </div>

                <!-- Initial State: Only Show Button -->
                <div x-show="!clientFormVisible" class="flex flex-col items-center justify-center py-8">
                    <x-button type="button" @click="clientModalOpen = true" variant="primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Customer
                    </x-button>
                    <x-text variant="muted" class="mt-4 text-center">
                        Search for an existing customer or enter a new one manually.
                    </x-text>
                </div>

                <!-- Form State: Show Fields -->
                <div x-show="clientFormVisible" style="display: none;" class="space-y-5">
                    <div>
                        <x-text variant="label" class="mb-1.5">Phone Number</x-text>
                        <div class="relative">
                            <input type="text" x-model="clientPhone" :readonly="clientFound"
                                class="w-full h-10 pl-3 pr-10 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition disabled:opacity-50 read-only:bg-gray-100 dark:read-only:bg-gray-800/50">
                            <button type="button" @click="resetClient"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6M5 7h14l-1 14H6L5 7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Customer Name</x-text>
                        <input type="text" x-model="clientName" :readonly="clientFound"
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition read-only:bg-gray-100 dark:read-only:bg-gray-800/50">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Information</x-text>
                        <textarea x-model="clientInfo" :readonly="clientFound" rows="3"
                            class="w-full p-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition read-only:bg-gray-100 dark:read-only:bg-gray-800/50"></textarea>
                    </div>
                </div>
            </div>

            <!-- Transaction Information -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <x-text variant="heading">Transaction Information</x-text>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-text variant="label" class="mb-1.5">No Invoice</x-text>
                        <input type="text" value="Auto-generated on save" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Transaction Type</x-text>
                        @php
                            $trxType = request('type', 'direct_order');
                            $trxTypeFormatted = ucwords(str_replace('_', ' ', $trxType));
                        @endphp
                        <input type="text" value="{{ $trxTypeFormatted }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500 font-semibold">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Transaction Date</x-text>
                        <input type="text" value="{{ now()->format('Y-m-d H:i') }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Last Update</x-text>
                        <input type="text" value="{{ now()->format('Y-m-d H:i') }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Item Status</x-text>
                        <input type="text" value="In Progress" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500 font-semibold">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Payment Status</x-text>
                        <input type="text" value="Unpaid" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500 font-semibold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Order Items List (Full Width) -->
        <div class="w-full">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex justify-between items-center">
                    <x-text variant="heading">Order Items List</x-text>
                    <x-button type="button" @click="productModalOpen = true" variant="primary" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Product
                    </x-button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-4">Product</th>
                                <th class="px-4 py-4">Variant/Size</th>
                                <th class="px-4 py-4 text-right">Price</th>
                                <th class="px-4 py-4 text-center">Qty</th>
                                <th class="px-4 py-4 text-right">Disc.</th>
                                <th class="px-4 py-4 text-right">Subtotal</th>
                                <th class="px-4 py-4 text-center">Act</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition border-b border-transparent"
                                    :class="{ 'border-rose-300 bg-rose-50/50 dark:bg-rose-900/10 dark:border-rose-900/30': errors['items.'+index+'.qty'] }">
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white" x-text="item.product_name"></div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-gray-600 dark:text-gray-400 font-medium" x-text="item.variant_name"></div>
                                        <div class="text-xs text-indigo-500 font-bold mt-0.5" x-text="'Size ' + item.size_name"></div>
                                    </td>
                                    <td class="px-4 py-4 text-right text-gray-600 dark:text-gray-450 font-mono font-bold" x-text="formatRupiah(item.price)"></td>
                                    
                                    <!-- QTY Input -->
                                    <td class="px-4 py-4 text-center min-w-[100px]">
                                        <input type="number" x-model.number="item.qty" @input="validateQty(index)" min="1"
                                            class="w-20 h-9 px-2 text-center text-sm font-extrabold rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 outline-none transition">
                                        <div x-show="errors['items.'+index+'.qty']" class="text-xs text-rose-500 mt-1 font-medium" x-text="errors['items.'+index+'.qty'][0]"></div>
                                    </td>

                                    <!-- Discount Input -->
                                    <td class="px-4 py-4 text-right min-w-[130px]">
                                        <div class="relative inline-block">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
                                            <input type="number" x-model.number="item.discount" @input="validateDiscount(index)" min="0"
                                                class="w-28 h-9 pl-7 pr-1.5 text-right text-sm font-bold rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/30 outline-none transition">
                                        </div>
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="px-4 py-4 text-right font-extrabold text-indigo-650 dark:text-indigo-400 font-mono" x-text="formatRupiah((item.price - item.discount) * item.qty)"></td>
                                    
                                    <!-- Action -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 p-1.5 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 rounded-md transition tooltip" title="Remove">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="items.length === 0">
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="mx-auto h-12 w-12 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p>No order items added yet. Please use the button above to add products.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals & Actions block -->
                <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/10 flex flex-col md:flex-row justify-between items-start md:items-end p-5">
                    
                    <!-- Left Side Actions -->
                    <div class="w-full md:w-auto mt-6 md:mt-0 order-2 md:order-1 flex gap-3 flex-col sm:flex-row pt-4 md:pt-0">
                        <x-button type="button" @click="submitOrder('{{ route('transactions.index') }}')"
                            variant="primary" x-bind:disabled="items.length === 0" class="justify-center h-11 px-6">
                            Create Order
                        </x-button>
                    </div>

                    <!-- Right Side Totals -->
                    <div class="w-full md:w-96 space-y-3 order-1 md:order-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100" x-text="formatRupiah(subtotal)"></span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400 pt-1">Overall Discount</span>
                            <input type="number" x-model="overallDiscount"
                                class="w-32 h-9 px-3 text-right text-sm rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500/50 outline-none transition"
                                min="0" placeholder="0">
                        </div>

                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <span class="font-extrabold text-gray-900 dark:text-gray-100 uppercase tracking-wider text-sm">Grand Total</span>
                            <span class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 leading-none"
                                x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Client Search Modal -->
        <div x-cloak x-show="clientModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="clientModalOpen = false"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-sm p-6 flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center mb-4">
                    <x-text variant="heading">Search Customer</x-text>
                    <button type="button" @click="clientModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 flex-1 overflow-hidden flex flex-col">
                    <div>
                        <x-text variant="label" class="mb-1.5">Search Name or Phone Number</x-text>
                        <div class="relative">
                            <input type="text" x-model="searchQuery" 
                                placeholder="Type name or phone..."
                                class="w-full h-10 pl-9 pr-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- Search Results (Searchable Dropdown list) -->
                    <div class="flex-1 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-100 dark:divide-gray-700/50 bg-gray-50 dark:bg-gray-900/50">
                        <template x-for="client in filteredClients" :key="client.id">
                            <button type="button" @click="selectExistingClient(client)"
                                class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition flex justify-between items-center group">
                                <div class="space-y-0.5">
                                    <div class="text-sm font-semibold text-gray-850 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition" x-text="client.client_name"></div>
                                    <div class="text-xs text-gray-500 font-mono" x-text="client.phone_number"></div>
                                </div>
                                <span class="text-[10px] bg-gray-200/70 dark:bg-gray-800 text-gray-605 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 group-hover:text-indigo-650 dark:group-hover:text-indigo-300 transition">Select</span>
                            </button>
                        </template>
                        <div x-show="filteredClients.length === 0" class="p-6 text-center text-xs text-gray-450 dark:text-gray-500">
                            No customers match your search.
                        </div>
                    </div>

                    <div class="border-t border-gray-150 dark:border-gray-700 pt-3 flex flex-col gap-2">
                        <x-button type="button" @click="proceedAsNewCustomer" variant="indigo" class="w-full h-10 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Create New Customer
                        </x-button>
                        
                        <x-button type="button" @click="clientModalOpen = false" variant="outline" class="w-full h-10">
                            Close
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Add Product Modal -->
        <div x-cloak x-show="productModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="productModalOpen = false; resetProductForm()"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-4xl p-6 flex flex-col max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <x-text variant="heading">Search & Add Products</x-text>
                    <button type="button" @click="productModalOpen = false; resetProductForm()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Filter Section (Dropdown & Search Variant) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-150 dark:border-gray-700/50">
                    <div>
                        <x-text variant="label" class="mb-1.5 ml-0.5">Filter by Product Name</x-text>
                        <select x-model="filterProduct" class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 outline-none cursor-pointer">
                            <option value="">All Products</option>
                            <template x-for="p in products" :key="p.id">
                                <option :value="p.id" x-text="p.product_name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5 ml-0.5">Search Variant Name</x-text>
                        <input type="text" x-model="filterVariant" placeholder="Type variant name..." 
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 outline-none">
                    </div>
                </div>

                <!-- Selectable Products Table -->
                <div class="flex-1 overflow-y-auto border border-gray-200 dark:border-gray-750 rounded-lg bg-gray-50/50 dark:bg-gray-900/10">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 sticky top-0 shadow-sm z-10">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">
                                    <input type="checkbox" @change="toggleSelectAll($event)" class="rounded border-gray-300 dark:border-gray-700 text-indigo-650 focus:ring-indigo-500 focus:ring-2">
                                </th>
                                <th class="px-4 py-3">Product Name</th>
                                <th class="px-4 py-3">Variant Name</th>
                                <th class="px-4 py-3">Product Type</th>
                                <th class="px-4 py-3 text-center">Size</th>
                                <th class="px-4 py-3 text-center">Stock</th>
                                <th class="px-4 py-3 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-850">
                            <template x-for="item in filteredSelectableItems" :key="item.stock_id">
                                <tr class="hover:bg-indigo-50/30 dark:hover:bg-gray-850/50 transition">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" x-model="item.selected" 
                                            :disabled="transactionType === 'direct_order' && item.stock <= 0"
                                            class="rounded border-gray-300 dark:border-gray-700 text-indigo-650 focus:ring-indigo-500 focus:ring-2 disabled:opacity-30 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" x-text="item.product_name"></td>
                                    <td class="px-4 py-3 text-gray-650 dark:text-gray-300" x-text="item.variant_name"></td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-455" x-text="item.product_type_name"></td>
                                    <td class="px-4 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400" x-text="item.size_name"></td>
                                    <td class="px-4 py-3 text-center font-bold">
                                        <span class="px-2 py-0.5 rounded-full text-xs"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-300': item.stock > 10,
                                                'bg-amber-100 text-amber-800 dark:bg-amber-955/40 dark:text-amber-300': item.stock > 0 && item.stock <= 10,
                                                'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-300': item.stock <= 0
                                            }"
                                            x-text="item.stock"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-gray-900 dark:text-gray-100" x-text="formatRupiah(item.price)"></td>
                                </tr>
                            </template>
                            <tr x-show="filteredSelectableItems.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    No products matching search filters.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3 mt-4">
                    <x-button type="button" @click="productModalOpen = false; resetProductForm()" variant="outline" class="h-10 px-5">
                        Cancel
                    </x-button>
                    <x-button type="button" @click="addSelectedItems" variant="indigo" class="h-10 px-6">
                        Add Selected Items
                    </x-button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pos', () => ({
                clients: @json($clients),
                products: @json($products),

                // Client info state
                searchQuery: '',
                clientPhone: '',
                clientName: '',
                clientInfo: '',
                clientFormVisible: false,
                clientFound: false,
                clientModalOpen: false,

                get filteredClients() {
                    if (!this.searchQuery.trim()) {
                        return this.clients;
                    }
                    const query = this.searchQuery.toLowerCase();
                    return this.clients.filter(c => 
                        (c.client_name && c.client_name.toLowerCase().includes(query)) ||
                        (c.phone_number && c.phone_number.includes(query))
                    );
                },

                selectExistingClient(client) {
                    this.clientPhone = client.phone_number;
                    this.clientName = client.client_name;
                    this.clientInfo = client.information || '';
                    this.clientFound = true;
                    this.clientFormVisible = true;
                    this.clientModalOpen = false;
                },

                proceedAsNewCustomer() {
                    const query = this.searchQuery.trim();
                    this.clientPhone = '';
                    this.clientName = '';
                    this.clientInfo = '';
                    
                    if (/^\+?[0-9\s\-]+$/.test(query)) {
                        this.clientPhone = query;
                    } else {
                        this.clientName = query;
                    }
                    
                    this.clientFound = false;
                    this.clientFormVisible = true;
                    this.clientModalOpen = false;
                },

                resetClient() {
                    this.searchQuery = '';
                    this.clientPhone = '';
                    this.clientName = '';
                    this.clientInfo = '';
                    this.clientFound = false;
                    this.clientFormVisible = false;
                },

                // Product info state
                productModalOpen: false,
                filterProduct: '',
                filterVariant: '',
                selectableItems: [],
                transactionType: '{{ request('type', 'direct_order') }}',

                init() {
                    let list = [];
                    this.products.forEach(p => {
                        p.variants.forEach(v => {
                            v.stocks.forEach(s => {
                                list.push({
                                    stock_id: s.id,
                                    product_id: p.id,
                                    variant_id: v.id,
                                    size_option_id: s.size_option_id,
                                    product_name: p.product_name,
                                    variant_name: v.variant_name,
                                    product_type_name: v.product_type ? v.product_type.name : '-',
                                    size_name: s.size_option ? s.size_option.name : '-',
                                    stock: s.stock,
                                    price: parseFloat(s.price),
                                    selected: false
                                });
                            });
                        });
                    });
                    this.selectableItems = list;
                },

                get filteredSelectableItems() {
                    let list = this.selectableItems;
                    if (this.filterProduct) {
                        list = list.filter(item => item.product_id == this.filterProduct);
                    }
                    if (this.filterVariant.trim()) {
                        let q = this.filterVariant.toLowerCase();
                        list = list.filter(item => 
                            (item.variant_name && item.variant_name.toLowerCase().includes(q))
                        );
                    }
                    return list;
                },

                toggleSelectAll(event) {
                    let checked = event.target.checked;
                    this.filteredSelectableItems.forEach(item => {
                        if (this.transactionType !== 'direct_order' || item.stock > 0) {
                            item.selected = checked;
                        }
                    });
                },

                addSelectedItems() {
                    let addedCount = 0;
                    this.selectableItems.forEach(item => {
                        if (item.selected) {
                            let existing = this.items.find(i => 
                                i.product_id == item.product_id && 
                                i.variant_id == item.variant_id && 
                                i.size_option_id == item.size_option_id
                            );
                            if (!existing) {
                                this.items.push({
                                    product_id: item.product_id,
                                    variant_id: item.variant_id,
                                    size_option_id: item.size_option_id,
                                    product_name: item.product_name,
                                    variant_name: item.variant_name,
                                    size_name: item.size_name,
                                    qty: 1,
                                    price: item.price,
                                    discount: 0
                                });
                                addedCount++;
                            }
                            item.selected = false;
                        }
                    });
                    
                    if (addedCount === 0) {
                        alert('Please select at least one new item to add.');
                        return;
                    }
                    
                    this.resetProductForm();
                    this.productModalOpen = false;
                },

                validateQty(index) {
                    let item = this.items[index];
                    if (item.qty === '' || item.qty === null || isNaN(item.qty)) {
                        return;
                    }
                    if (item.qty < 1) {
                        item.qty = 1;
                    }
                    
                    if (this.transactionType === 'direct_order') {
                        let maxStock = 0;
                        let matched = this.selectableItems.find(s => 
                            s.product_id == item.product_id && 
                            s.variant_id == item.variant_id && 
                            s.size_option_id == item.size_option_id
                        );
                        if (matched) {
                            maxStock = matched.stock;
                        }
                        
                        if (item.qty > maxStock) {
                            alert('Quantity cannot exceed available stock (' + maxStock + ') for Direct Orders.');
                            item.qty = maxStock > 0 ? maxStock : 1;
                        }
                    }
                },

                validateDiscount(index) {
                    let item = this.items[index];
                    if (item.discount === '' || item.discount === null || isNaN(item.discount)) {
                        return;
                    }
                    if (item.discount < 0) {
                        item.discount = 0;
                    }
                    if (item.discount > item.price) {
                        alert('Discount cannot exceed the selling price.');
                        item.discount = item.price;
                    }
                },

                items: [],
                errors: {},
                overallDiscount: 0,

                // Computed subtotals (using the requested formula: subtotal = (price - disc) * QTY)
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + ((item.price - item.discount) * item.qty), 0);
                },

                get grandTotal() {
                    let total = this.subtotal - parseFloat(this.overallDiscount || 0);
                    return total > 0 ? total : 0;
                },

                resetProductForm() {
                    this.filterProduct = '';
                    this.filterVariant = '';
                    this.selectableItems.forEach(item => {
                        item.selected = false;
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
                        .format(number)
                        .replace('Rp', 'Rp ')
                        .replace(',00', '');
                },

                async submitOrder(redirectUrl) {
                    if (this.items.length === 0) return;
                    if (!this.clientName || !this.clientPhone) {
                        alert('Please fill customer phone and name');
                        return;
                    }
                    
                    this.errors = {}; // Reset errors before submitting

                    try {
                        let response = await fetch('{{ route('transactions.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                client_phone: this.clientPhone,
                                client_name: this.clientName,
                                client_info: this.clientInfo,
                                overall_discount: parseFloat(this.overallDiscount || 0),
                                transaction_type: '{{ request('type', 'direct_order') }}',
                                items: this.items
                            })
                        });

                        const result = await response.json();

                        if (response.ok) {
                            if (window.FilamentNotification) {
                                // Or a toast system if configured
                            } else {
                                alert('Transaction created successfully!');
                            }
                            
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            } else {
                                window.location.href = redirectUrl;
                            }
                        } else if (response.status === 422) {
                            // Validation error
                            this.errors = result.errors || {};
                            alert('Stock not sufficient or validation failed. Please check the items.');
                        } else {
                            alert(result.message || 'Failed to create order. Please check the data.');
                        }
                    } catch (error) {
                        alert('Network error occurred.');
                    }
                }
            }));
        });
    </script>
</x-filament-panels::layout>