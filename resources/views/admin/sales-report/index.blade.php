<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ __('transaction.sales_report') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('transaction.sales_report_help') }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden p-6">
                <form action="{{ route('sales-report.index') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="show_data" value="1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        {{-- Start Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('transaction.start_date') }}</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        
                        {{-- End Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('transaction.end_date') }}</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        {{-- Transaction Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('transaction.trx_status') }}</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('transaction.all_status') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('transaction.cancelled') }}</option>
                                <option value="on progress" {{ request('status') == 'on progress' ? 'selected' : '' }}>{{ __('transaction.on_progress') }}</option>
                                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>{{ __('transaction.done') }}</option>
                            </select>
                        </div>

                        {{-- Payment Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('transaction.payment_status') }}</label>
                            <select name="payment_status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('transaction.all_payment') }}</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>{{ __('transaction.unpaid') }}</option>
                                <option value="deposit" {{ request('payment_status') == 'deposit' ? 'selected' : '' }}>{{ __('transaction.deposit') }}</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('transaction.paid') }}</option>
                            </select>
                        </div>

                        {{-- Item Status --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('transaction.item_status') }}</label>
                            <select name="item_status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('transaction.all_item_status') }}</option>
                                <option value="in_progress" {{ request('item_status') == 'in_progress' ? 'selected' : '' }}>{{ __('transaction.in_progress') }}</option>
                                <option value="awaiting_pickup" {{ request('item_status') == 'awaiting_pickup' ? 'selected' : '' }}>{{ __('transaction.awaiting_pickup') }}</option>
                                <option value="collected" {{ request('item_status') == 'collected' ? 'selected' : '' }}>{{ __('transaction.collected') }}</option>
                            </select>
                        </div>

                        {{-- Transaction Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('transaction.trx_type') }}</label>
                            <select name="transaction_type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('transaction.all_types') }}</option>
                                <option value="pre_order" {{ request('transaction_type') == 'pre_order' ? 'selected' : '' }}>{{ __('transaction.pre_order') }}</option>
                                <option value="direct_order" {{ request('transaction_type') == 'direct_order' ? 'selected' : '' }}>{{ __('transaction.direct_order') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <a href="{{ route('sales-report.index') }}" class="px-4 py-2 mr-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            {{ __('transaction.reset') }}
                        </a>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            {{ __('transaction.show_data') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            @if($isFiltered)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('transaction.result_data') }}</h3>
                        @if(count($transactions) > 0)
                            <a href="{{ route('sales-report.export', request()->query()) }}" 
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('transaction.download_excel') }}
                            </a>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.no') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.tgl_trx') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.inv_no') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.client') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.payment_status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.trx_status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.total_price') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.overall_discount') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.grand_total') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.total_paid') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ __('transaction.user') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-800">
                                @forelse($transactions as $index => $trx)
                                    @php
                                        $totalHarga = $trx->details->sum(fn($d) => $d->price * $d->quantity);
                                        $totalPaid = $trx->payments->sum('amount');
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $trx->created_at ? \Carbon\Carbon::parse($trx->created_at)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $trx->trx_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $trx->client->client_name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($trx->payment_status === 'paid')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ __('transaction.paid') }}</span>
                                            @elseif($trx->payment_status === 'deposit')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">{{ __('transaction.deposit') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ __('transaction.unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($trx->status === 'done')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ __('transaction.done') }}</span>
                                            @elseif($trx->status === 'on progress')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ __('transaction.on_progress') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400">{{ __('transaction.cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                            Rp {{ number_format($totalHarga, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-rose-600 dark:text-rose-400">
                                            Rp {{ number_format($trx->discount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $trx->logs->first()->user->name ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            {{ __('transaction.no_data_found_filters') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            {{-- Optional Table Footer for Totals --}}
                            @if(count($transactions) > 0)
                                <tfoot class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900 dark:text-white">{{ __('transaction.total_keseluruhan') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900 dark:text-white">
                                            Rp {{ number_format($transactions->sum(fn($t) => $t->details->sum(fn($d) => $d->price * $d->quantity)), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-rose-600 dark:text-rose-400">
                                            Rp {{ number_format($transactions->sum('discount'), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900 dark:text-white">
                                            Rp {{ number_format($transactions->sum('grand_total'), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($transactions->sum(fn($t) => $t->payments->sum('amount')), 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">{{ __('transaction.no_data_displayed') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('transaction.no_data_displayed_help') }}</p>
                </div>
            @endif

        </div>
    </div>
</x-filament-panels::layout>
