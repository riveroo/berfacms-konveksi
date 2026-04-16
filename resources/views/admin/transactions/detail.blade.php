<x-filament-panels::layout>
    <div x-data="transactionDetail()" class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <x-text variant="title">Transaction Detail: {{ $transaction->trx_id }}</x-text>
                <div class="flex items-center gap-2 mt-1">
                    <x-text variant="muted">Manage order status and payment</x-text>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                        @if($transaction->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($transaction->status === 'on progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($transaction->status === 'done') bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300
                        @elseif($transaction->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif
                    ">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>
            <x-button variant="outline" href="{{ route('transactions.index') }}">
                Back to List
            </x-button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(in_array($transaction->status, ['waiting for payment', 'paid', 'on progress']))
            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm flex items-center justify-end gap-3">

                @if($transaction->status === 'waiting for payment')
                    <!-- Cancel Button -->
                    <form action="{{ route('transactions.cancel', $transaction->id) }}" method="POST" class="inline">
                        @csrf
                        <x-button type="submit" variant="danger"
                            onclick="return confirm('Are you sure you want to cancel this order?')">
                            Cancel Order
                        </x-button>
                    </form>

                    <!-- Input Payment Button -->
                    <x-button type="button" @click="paymentModalOpen = true" variant="primary">
                        Input Payment
                    </x-button>

                    <!-- Edit Button -->
                    <x-button href="{{ url('/admin/transactions/' . $transaction->id . '/edit') }}" variant="outline">
                        Edit
                    </x-button>
                @endif

                @if(in_array($transaction->status, ['paid', 'on progress']))
                    <!-- Update Status Button -->
                    <x-button type="button" @click="statusModalOpen = true" variant="primary">
                        Update Status
                    </x-button>
                @endif

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
                        <input type="text" value="{{ optional($transaction->client)->phone_number }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 outline-none">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Customer Name</x-text>
                        <input type="text" value="{{ optional($transaction->client)->client_name }}" readonly
                            class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 outline-none">
                    </div>
                    <div>
                        <x-text variant="label" class="mb-1.5">Information</x-text>
                        <textarea readonly rows="2"
                            class="w-full p-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 outline-none">{{ optional($transaction->client)->information }}</textarea>
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
                            @forelse($transaction->details as $item)
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
                            <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400 pt-1">Overall Discount</span>
                            <span class="font-bold text-rose-500">-Rp {{ number_format($transaction->total_discount, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <span class="font-extrabold text-gray-900 dark:text-gray-100 uppercase tracking-wider text-sm">Grand Total</span>
                            <span class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 leading-none">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-cloak x-show="paymentModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="paymentModalOpen = false"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-sm p-6 overflow-hidden">
                <x-text variant="heading" class="mb-4">Input Payment</x-text>

                <form action="{{ route('transactions.payment', $transaction->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-text variant="label" class="mb-1.5">Bank Name</x-text>
                            <input type="text" name="bank_name" required placeholder="e.g BCA"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>
                        <div>
                            <x-text variant="label" class="mb-1.5">Account Number</x-text>
                            <input type="text" name="account_number" required
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>
                        <div>
                            <x-text variant="label" class="mb-1.5">Account Name</x-text>
                            <input type="text" name="account_name" required
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>
                        <div>
                            <x-text variant="label" class="mb-1.5">Transfer Amount</x-text>
                            <input type="number" name="transfer_amount" value="{{ (int) $transaction->grand_total }}"
                                required min="0"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <x-button type="button" @click="paymentModalOpen = false" variant="outline" size="sm">
                                Cancel
                            </x-button>
                            <x-button type="submit" variant="primary" size="sm">
                                Save Payment
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Status Modal -->
        <div x-cloak x-show="statusModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
            <div @click.away="statusModalOpen = false"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-sm p-6 overflow-hidden">
                <x-text variant="heading" class="mb-4">Update Status</x-text>

                <form action="{{ route('transactions.status', $transaction->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-text variant="label" class="mb-1.5">Change Status</x-text>
                            <select name="status"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                                <option value="on progress" {{ $transaction->status === 'on progress' ? 'selected' : '' }}>On Progress</option>
                                <option value="done" {{ $transaction->status === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <x-button type="button" @click="statusModalOpen = false" variant="outline" size="sm">
                                Cancel
                            </x-button>
                            <x-button type="submit" variant="primary" size="sm">
                                Update Status
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('transactionDetail', () => ({
                paymentModalOpen: false,
                statusModalOpen: false,
            }));
        });
    </script>
</x-filament-panels::layout>