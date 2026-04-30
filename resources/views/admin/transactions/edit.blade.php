<x-filament-panels::layout>
    <div x-data="pos()" class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <x-text variant="title">Edit Transaction: {{ $transaction->trx_id }}</x-text>
                <x-text variant="muted">Update existing transaction data</x-text>
            </div>
            <x-button variant="outline" href="{{ route('transactions.detail', $transaction->id) }}">
                Back to Detail
            </x-button>
        </div>

        <!-- Action Buttons -->
        @if(!in_array($transaction->status, ['cancelled', 'done']))
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm flex items-center justify-end gap-3">
                <x-button type="button" @click="cancelOrder('{{ route('transactions.cancel', $transaction->id) }}')"
                    variant="danger" class="bg-red-500 hover:bg-red-600 text-white border-transparent">
                    Cancel Order
                </x-button>
                <x-button type="button" @click="submitOrder('{{ route('transactions.detail', $transaction->id) }}')"
                    variant="primary" x-bind:disabled="items.length === 0">
                    Save Changes
                </x-button>
            </div>
        @endif

        <!-- Top Section: Client Information & Transaction Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Client Information -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <x-text variant="heading">Customer Information</x-text>
                </div>

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
                        <input type="text" value="{{ $transaction->trx_id }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Transaction Type</x-text>
                        <select x-model="transactionType" :disabled="'{{ $transaction->transaction_type }}' === 'direct_order'"
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-800">
                            <option value="pre_order">Pre Order</option>
                            <option value="direct_order">Direct Order</option>
                        </select>
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Transaction Date</x-text>
                        <input type="text" value="{{ $transaction->created_at->format('Y-m-d H:i') }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Last Update</x-text>
                        <input type="text" value="{{ $transaction->updated_at->format('Y-m-d H:i') }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Item Status</x-text>
                        <select x-model="itemStatus" :disabled="transactionType === 'pre_order'"
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-800">
                            <option value="in_progress">In Progress</option>
                            <option value="awaiting_pickup">Awaiting Pickup</option>
                            <option value="collected">Collected</option>
                        </select>
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Payment Status</x-text>
                        <input type="text" value="{{ ucwords($transaction->payment_status) }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 focus:outline-none text-gray-500 font-semibold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Order Items List (Full Width) -->
        <div class="w-full">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20 flex justify-between items-center">
                    <div>
                        <x-text variant="heading">Order Items List</x-text>
                        @if($transaction->status === 'on progress' && $transaction->item_status === 'collected')
                            <p class="text-xs text-rose-500 mt-1 font-medium">Items cannot be modified after collected</p>
                        @endif
                        @if($transaction->payment_status === 'paid')
                            <p class="text-xs text-rose-500 mt-1 font-medium">Items cannot be modified after payment is completed</p>
                        @endif
                    </div>
                    @if(!($transaction->status === 'on progress' && $transaction->item_status === 'collected') && $transaction->payment_status !== 'paid')
                    <x-button type="button" @click="productModalOpen = true" variant="primary" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Product
                    </x-button>
                    @endif
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
                                        <div class="text-gray-600 dark:text-gray-400" x-text="item.variant_name"></div>
                                        <div class="text-xs text-gray-500 mt-0.5" x-text="'Size ' + item.size_name"></div>
                                    </td>
                                    <td class="px-4 py-4 text-right text-gray-600 dark:text-gray-400" x-text="formatRupiah(item.price)"></td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="font-bold text-gray-900 dark:text-white" x-text="item.qty"></div>
                                        <div x-show="errors['items.'+index+'.qty']" class="text-xs text-rose-500 mt-1 font-medium whitespace-nowrap" x-text="errors['items.'+index+'.qty'][0]"></div>
                                    </td>
                                    <td class="px-4 py-4 text-right text-rose-500 font-medium" x-text="item.discount > 0 ? '-' + formatRupiah(item.discount) : '-'"></td>
                                    <td class="px-4 py-4 text-right font-bold text-gray-900 dark:text-white" x-text="formatRupiah((item.price * item.qty) - item.discount)"></td>
                                    <td class="px-4 py-4 text-center">
                                        @if(!($transaction->status === 'on progress' && $transaction->item_status === 'collected') && $transaction->payment_status !== 'paid')
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="editItem(index)" class="text-indigo-500 hover:text-indigo-700 p-1.5 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 rounded-md transition tooltip" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </button>
                                            <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 p-1.5 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 rounded-md transition tooltip" title="Remove">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        @endif
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

                <!-- Totals block -->
                <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/10 flex flex-col items-end p-5">
                    
                    <!-- Right Side Totals -->
                    <div class="w-full md:w-96 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100" x-text="formatRupiah(subtotal)"></span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400 pt-1">Overall Discount</span>
                            <input type="number" x-model="overallDiscount" :disabled="('{{ $transaction->status }}' === 'on progress' && '{{ $transaction->item_status }}' === 'collected') || '{{ $transaction->payment_status }}' === 'paid'"
                                class="w-32 h-9 px-3 text-right text-sm rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500/50 outline-none transition disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-800"
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
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-sm p-6 overflow-hidden">
                <x-text variant="heading" class="mb-4">Search Customer</x-text>

                <div class="space-y-4">
                    <div>
                        <x-text variant="label" class="mb-1.5">Phone Number</x-text>
                        <input type="text" x-model="searchPhone" @keydown.enter="checkClient"
                            placeholder="Enter exact phone..."
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <x-button type="button" @click="clientModalOpen = false" variant="outline" size="sm">
                            Cancel
                        </x-button>
                        <x-button type="button" @click="checkClient" variant="primary" size="sm">
                            Check Account
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div x-cloak x-show="productModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="productModalOpen = false; resetProductForm()"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6 overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <x-text variant="heading" x-text="editIndex !== null ? 'Edit Product' : 'Add Product'">Add Product</x-text>
                    <button @click="productModalOpen = false; resetProductForm()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-5">
                    <div>
                        <x-text variant="label" class="mb-1.5">Product</x-text>
                        <select x-model="selectedProductId"
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            <option value="">-- Select Product --</option>
                            <template x-for="product in products" :key="product.id">
                                <option :value="product.id" x-text="product.product_name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-text variant="label" class="mb-1.5">Variant</x-text>
                            <select x-model="selectedVariantId" :disabled="!availableVariants.length"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none disabled:opacity-50">
                                <option value="">-- Select Variant --</option>
                                <template x-for="variant in availableVariants" :key="variant.id">
                                    <option :value="variant.id" x-text="variant.variant_name"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <x-text variant="label" class="mb-1.5">Size</x-text>
                            <select x-model="selectedSizeId" :disabled="!availableSizes.length"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none disabled:opacity-50">
                                <option value="">-- Size --</option>
                                <template x-for="size in availableSizes" :key="size.id">
                                    <option :value="size.id" x-text="size.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-text variant="label" class="mb-1.5">Price (Rp)</x-text>
                            <div class="w-full h-10 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold"
                                x-text="formatRupiah(currentPrice)"></div>
                        </div>
                        
                        <div>
                            <x-text variant="label" class="mb-1.5">Subtotal (Rp)</x-text>
                            <input type="text" :value="formatRupiah(itemSubtotal)" readonly
                                class="w-full h-10 px-3 text-sm font-bold rounded-lg border border-gray-200 dark:border-gray-700 bg-indigo-50/50 dark:bg-gray-800 text-indigo-700 dark:text-gray-300 outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-text variant="label" class="mb-1.5">Quantity</x-text>
                            <input type="number" x-model="inputQty" min="1" :max="currentStock"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                            <p x-cloak x-show="selectedSizeId && currentStock > 0" class="text-xs text-gray-500 mt-1">Available Stock: <span x-text="currentStock"></span></p>
                            <p x-cloak x-show="selectedSizeId && currentStock === 0" class="text-xs text-red-500 mt-1">Out of Stock</p>
                            <p x-cloak x-show="inputQty > currentStock && currentStock > 0" class="text-xs text-red-500 mt-1">Quantity exceeds available stock</p>
                        </div>
                        <div>
                            <x-text variant="label" class="mb-1.5">Discount total (Rp)</x-text>
                            <input type="number" x-model="inputDiscount" min="0"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-2">
                        <x-button type="button" @click="productModalOpen = false; resetProductForm()" variant="outline">
                            Cancel
                        </x-button>
                        <x-button type="button" @click="addItem" variant="primary" x-bind:disabled="!selectedSizeId || inputQty > currentStock || currentStock === 0">
                            <span x-text="editIndex !== null ? 'Update Item' : 'Add to List'"></span>
                        </x-button>
                    </div>
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
                searchPhone: '{{ $transaction->client->phone_number }}',
                clientPhone: '{{ $transaction->client->phone_number }}',
                clientName: '{{ $transaction->client->client_name }}',
                clientInfo: `{{ $transaction->client->information }}`,
                clientFormVisible: true,
                clientFound: true,
                clientModalOpen: false,

                // Transaction config
                transactionType: '{{ $transaction->transaction_type }}',
                itemStatus: '{{ $transaction->item_status }}',
                paymentModalOpen: false,

                checkClient() {
                    if (this.searchPhone.trim() === '') {
                        this.clientFound = false;
                    } else {
                        let found = this.clients.find(c => c.phone_number === this.searchPhone);
                        if (found) {
                            this.clientName = found.client_name;
                            this.clientInfo = found.information || '';
                            this.clientFound = true;
                        } else {
                            this.clientName = '';
                            this.clientInfo = '';
                            this.clientFound = false;
                        }
                    }
                    this.clientPhone = this.searchPhone;
                    this.clientFormVisible = true;
                    this.clientModalOpen = false;
                },

                resetClient() {
                    this.searchPhone = '';
                    this.clientPhone = '';
                    this.clientName = '';
                    this.clientInfo = '';
                    this.clientFound = false;
                    this.clientFormVisible = false;
                },

                // Product info state
                productModalOpen: false,
                editIndex: null,
                selectedProductId: '',
                selectedVariantId: '',
                selectedSizeId: '',
                inputQty: 1,
                inputDiscount: 0,

                get selectedProduct() {
                    return this.products.find(p => p.id == this.selectedProductId);
                },
                get availableVariants() {
                    return this.selectedProduct ? this.selectedProduct.variants : [];
                },
                get selectedVariant() {
                    return this.availableVariants.find(v => v.id == this.selectedVariantId);
                },
                get availableSizes() {
                    if (!this.selectedVariant) return [];
                    return this.selectedVariant.stocks.map(s => s.size_option).filter(s => s);
                },
                get currentPrice() {
                    if (this.editIndex !== null) {
                        let originalItem = this.items[this.editIndex];
                        if (originalItem.product_id == this.selectedProductId && 
                            originalItem.variant_id == this.selectedVariantId && 
                            originalItem.size_option_id == this.selectedSizeId) {
                            return originalItem.price;
                        }
                    }
                    if (!this.selectedVariant || !this.selectedSizeId) return 0;
                    let stock = this.selectedVariant.stocks.find(s => s.size_option_id == this.selectedSizeId);
                    return stock ? stock.price : 0;
                },
                get currentStock() {
                    if (!this.selectedVariant || !this.selectedSizeId) return 0;
                    let stock = this.selectedVariant.stocks.find(s => s.size_option_id == this.selectedSizeId);
                    return stock ? stock.stock : 0;
                },
                get itemSubtotal() {
                    let price = parseFloat(this.currentPrice) || 0;
                    let qty = parseInt(this.inputQty) || 0;
                    let disc = parseFloat(this.inputDiscount) || 0;
                    let sub = (price * qty) - disc;
                    return sub > 0 ? sub : 0;
                },

                items: [
                    @foreach($transaction->details as $detail)
                    {
                        product_id: {{ $detail->product_id }},
                        variant_id: {{ $detail->variant_id }},
                        size_option_id: {{ $detail->size_option_id }},
                        product_name: `{{ optional($detail->product)->product_name ?? '' }}`,
                        variant_name: `{{ optional($detail->variant)->variant_name ?? '' }}`,
                        size_name: `{{ optional($detail->sizeOption)->name ?? '' }}`,
                        qty: {{ $detail->quantity }},
                        price: {{ $detail->price }},
                        discount: {{ $detail->discount }}
                    },
                    @endforeach
                ],
                errors: {},
                overallDiscount: {{ $transaction->total_discount - $transaction->details->sum('discount') }},

                // Computed subtotals
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + ((item.price * item.qty) - item.discount), 0);
                },

                get grandTotal() {
                    let total = this.subtotal - parseFloat(this.overallDiscount || 0);
                    return total > 0 ? total : 0;
                },

                resetProductForm() {
                    this.selectedProductId = '';
                    this.selectedVariantId = '';
                    this.selectedSizeId = '';
                    this.inputQty = 1;
                    this.inputDiscount = 0;
                    this.editIndex = null;
                },

                editItem(index) {
                    this.editIndex = index;
                    let item = this.items[index];
                    
                    this.selectedProductId = item.product_id;
                    setTimeout(() => {
                        this.selectedVariantId = item.variant_id;
                        setTimeout(() => {
                            this.selectedSizeId = item.size_option_id;
                        }, 50);
                    }, 50);
                    
                    this.inputQty = item.qty;
                    this.inputDiscount = item.discount;
                    
                    // Open Modal
                    this.productModalOpen = true;
                },

                addItem() {
                    if (!this.selectedProductId || !this.selectedVariantId || !this.selectedSizeId || this.inputQty < 1) {
                        alert('Please select product, variant, size and enter a valid quantity.');
                        return;
                    }

                    let product = this.selectedProduct;
                    let variant = this.selectedVariant;
                    let size = this.availableSizes.find(s => s.id == this.selectedSizeId);
                    let price = this.currentPrice;

                    // Compute the item
                    let itemObj = {
                        product_id: product.id,
                        variant_id: variant.id,
                        size_option_id: size.id,
                        product_name: product.product_name,
                        variant_name: variant.variant_name,
                        size_name: size.name,
                        qty: parseInt(this.inputQty),
                        price: parseFloat(price),
                        discount: parseFloat(this.inputDiscount || 0)
                    };

                    if (this.editIndex !== null) {
                        this.items[this.editIndex] = itemObj;
                        this.editIndex = null;
                    } else {
                        this.items.push(itemObj);
                    }

                    this.resetProductForm();
                    this.productModalOpen = false;
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

                cancelOrder(url) {
                    if (confirm('Are you sure you want to cancel this order?')) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;
                        form.innerHTML = `
                            @csrf
                            @method('POST')
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                },

                async submitOrder(redirectUrl) {
                    if (this.items.length === 0) return;
                    if (!this.clientName || !this.clientPhone) {
                        alert('Please fill customer phone and name');
                        return;
                    }

                    this.errors = {};

                    try {
                        let response = await fetch('{{ route('transactions.update', $transaction->id) }}', {
                            method: 'PUT',
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
                                transaction_type: this.transactionType,
                                item_status: this.transactionType === 'pre_order' ? 'in_progress' : this.itemStatus,
                                items: this.items
                            })
                        });

                        const result = await response.json();

                        if (response.ok) {
                            alert('Transaction successfully updated!');
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            } else {
                                window.location.href = redirectUrl;
                            }
                        } else if (response.status === 422) {
                            this.errors = result.errors || {};
                            alert('Validation failed or stock not sufficient. Please check the items.');
                        } else {
                            alert(result.message || 'Failed to update transaction. Please check the data.');
                        }
                    } catch (error) {
                        alert('Network error occurred.');
                    }
                }
            }));
        });
    </script>
</x-filament-panels::layout>