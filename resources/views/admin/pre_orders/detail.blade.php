<x-filament-panels::layout>
    <div x-data="{ acceptModalOpen: false }" class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <x-text variant="title">Pre Order Detail: {{ $preOrder->po_id }}</x-text>
                <div class="flex items-center gap-2 mt-1">
                    <x-text variant="muted">Manage and review pre order details</x-text>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                        @if($preOrder->status === 'accepted') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($preOrder->status === 'on process') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($preOrder->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 @endif
                    ">
                        {{ ucfirst($preOrder->status) }}
                    </span>
                </div>
            </div>
            <x-button variant="outline" href="{{ route('pre-orders.index') }}">
                Back to List
            </x-button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400"
                role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/20 dark:text-red-400"
                role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($preOrder->status === 'accepted' && $preOrder->transaction)
            <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 flex items-center justify-between" role="alert">
                <div>
                    <strong>Accepted!</strong> This Pre-Order has been converted to Transaction: 
                    <a href="{{ route('transactions.detail', $preOrder->transaction->id) }}" class="font-bold underline">{{ $preOrder->transaction->trx_id }}</a>
                </div>
                <x-button variant="outline" size="sm" href="{{ route('transactions.detail', $preOrder->transaction->id) }}">
                    View Transaction
                </x-button>
            </div>
        @endif

        @if($preOrder->status === 'on process')
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm flex items-center justify-end gap-3">
                <form action="{{ route('pre-orders.reject', $preOrder->id) }}" method="POST" class="inline">
                    @csrf
                    <x-button type="submit" variant="danger"
                        onclick="return confirm('Are you sure you want to reject this PO?')">
                        Reject
                    </x-button>
                </form>

                <x-button type="button" @click="acceptModalOpen = true" variant="primary">
                    Accept PO
                </x-button>
            </div>
        @endif

        <!-- Customer Information Section -->
        <div class="max-w-2xl">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <x-text variant="heading">Customer Information</x-text>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-text variant="label" class="mb-1.5">Phone Number</x-text>
                        <input type="text" value="{{ optional($preOrder->client)->phone_number }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 outline-none">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Customer Name</x-text>
                        <input type="text" value="{{ optional($preOrder->client)->client_name }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 outline-none">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Information</x-text>
                        <textarea readonly rows="2"
                            class="w-full p-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 outline-none">{{ optional($preOrder->client)->information }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items List (Full Width) -->
        <div class="w-full">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                    <x-text variant="heading">Order Items List</x-text>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($preOrder->details as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ optional($item->product)->product_name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-gray-600 dark:text-gray-400">
                                            {{ optional($item->variant)->variant_name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Size
                                            {{ optional($item->sizeOption)->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                                    <td class="px-4 py-4 text-right text-rose-500 font-medium">
                                        {{ $item->discount > 0 ? '-Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-gray-900 dark:text-white">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500">No items available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/10 flex flex-col items-end p-5">
                    
                    <div class="w-full md:w-96 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total Price</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($preOrder->total_price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400 pt-1">Overall Discount</span>
                            <span class="font-bold text-rose-500">-Rp {{ number_format($preOrder->total_discount, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <span class="font-extrabold text-gray-900 dark:text-gray-100 uppercase tracking-wider text-sm">Grand Total</span>
                            <span class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 leading-none">Rp {{ number_format($preOrder->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Accept Status Modal -->
        <div x-cloak x-show="acceptModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="acceptModalOpen = false"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-sm p-6 overflow-hidden">
                <x-text variant="heading" class="mb-4">Accept Pre Order</x-text>

                <form action="{{ route('pre-orders.accept', $preOrder->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-text variant="label" class="mb-1.5">Initial Payment Status</x-text>
                            <select name="payment_status"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                                <option value="waiting for payment">Waiting for payment</option>
                                <option value="paid">Paid</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-2">
                                Accepting this Pre Order will convert it into a concrete Transaction and deduct the inventory stock accordingly.
                            </p>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <x-button type="button" @click="acceptModalOpen = false" variant="outline" size="sm">
                                Cancel
                            </x-button>
                            <x-button type="submit" variant="primary" size="sm">
                                Confirm Accept
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-filament-panels::layout>
