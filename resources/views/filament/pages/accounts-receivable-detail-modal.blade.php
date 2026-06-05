<div class="space-y-6 text-gray-900 dark:text-gray-100">
    {{-- Customer Information --}}
    <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
        <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">{{ __('transaction.customer_info') }}</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <span class="block text-xs text-gray-400">{{ __('transaction.customer_name') }}</span>
                <span class="text-base font-bold text-gray-900 dark:text-white">{{ $client->client_name }}</span>
            </div>
            <div>
                <span class="block text-xs text-gray-400">{{ __('transaction.phone_number') }}</span>
                <span class="text-base font-bold text-gray-900 dark:text-white">{{ $client->phone_number ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Transactions Breakdown --}}
    <div class="space-y-3">
        <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('transaction.transactions_breakdown') }}</h4>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-150 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3">{{ __('transaction.tgl_trx') }}</th>
                        <th class="px-4 py-3">{{ __('transaction.trx_type') }}</th>
                        <th class="px-4 py-3">{{ __('transaction.item_status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('transaction.price') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('transaction.total_transaction') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('transaction.total_paid') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('transaction.total_ar') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('transaction.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $transactions = $client->transactions()->where('status', '!=', 'cancelled')->latest()->get();
                    @endphp
                    @forelse ($transactions as $trx)
                        @php
                            $totalPaid = $trx->payments()->sum('amount');
                            $totalAR = $trx->grand_total - $totalPaid;
                            $trxTypeFormatted = $trx->transaction_type === 'pre_order' ? __('transaction.pre_order') : __('transaction.direct_order');
                            
                            $itemStatusFormatted = 'N/A';
                            if ($trx->item_status === 'in_progress') {
                                $itemStatusFormatted = __('transaction.in_progress');
                            } elseif ($trx->item_status === 'awaiting_pickup') {
                                $itemStatusFormatted = __('transaction.awaiting_pickup');
                            } elseif ($trx->item_status === 'collected') {
                                $itemStatusFormatted = __('transaction.collected');
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                {{ $trx->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap uppercase text-xs font-bold text-gray-700 dark:text-gray-300">
                                {{ $trxTypeFormatted }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 uppercase">
                                    {{ $itemStatusFormatted }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right font-medium text-gray-600 dark:text-gray-400">
                                Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($totalPaid, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right font-black text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($totalAR, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a 
                                    href="/admin/transactions/{{ $trx->id }}" 
                                    target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1 bg-primary-600 hover:bg-primary-500 text-white text-xs font-semibold rounded-lg shadow-sm transition"
                                >
                                    <span>{{ __('transaction.detail') }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 italic">
                                {{ __('transaction.no_active_transactions') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
