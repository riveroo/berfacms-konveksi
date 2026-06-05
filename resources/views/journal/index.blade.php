<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ __('finance.general_journal') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('finance.journal_subtitle') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-button 
                        href="{{ route('journal.export', ['filter_month' => $filterMonth]) }}" 
                        variant="primary"
                        target="_blank"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('finance.export_pdf') }}
                    </x-button>
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-4">
                <form method="GET" action="{{ route('journal.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="w-full sm:w-72">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.filter_month_year') }}</label>
                        <input 
                            type="month" 
                            name="filter_month" 
                            value="{{ $filterMonth }}" 
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                        >
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button 
                            type="submit" 
                            class="flex-1 sm:flex-initial px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white font-medium rounded-lg text-sm transition"
                        >
                            {{ __('finance.search') }}
                        </button>
                        <a 
                            href="{{ route('journal.index') }}" 
                            class="flex-1 sm:flex-initial text-center px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                        >
                            {{ __('finance.reset') }}
                        </a>
                    </div>
                </form>
            </div>

            {{-- Ledger Status Card --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('finance.total_debit') }}</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($totalDebit, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('finance.total_credit') }}</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($totalCredit, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl p-5 border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('finance.status') }}</p>
                        <div class="mt-1">
                            @if($totalDebit === $totalCredit)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-success-600 dark:text-success-400">
                                    {{ __('finance.balanced') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-danger-600 dark:text-danger-400">
                                    {{ __('finance.unbalanced') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($totalDebit === $totalCredit)
                            <div class="p-2 bg-success-50 dark:bg-success-900/20 rounded-full">
                                <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="p-2 bg-danger-50 dark:bg-danger-900/20 rounded-full">
                                <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-4 w-3/12">{{ __('finance.trx_date') }}</th>
                                <th class="px-6 py-4 w-4/12">{{ __('finance.coa') }}</th>
                                <th class="px-6 py-4 w-1/12">{{ __('finance.code') }}</th>
                                <th class="px-6 py-4 text-right w-2/12">{{ __('finance.debit') }}</th>
                                <th class="px-6 py-4 text-right w-2/12">{{ __('finance.credit') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-transparent">
                            @php
                                $prevEntryId = null;
                            @endphp
                            @forelse($details as $detail)
                                @php
                                    $isNewEntry = $detail->journal_entry_id !== $prevEntryId;
                                    $prevEntryId = $detail->journal_entry_id;
                                @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors {{ $isNewEntry && !$loop->first ? 'border-t border-gray-200 dark:border-gray-800' : '' }}">
                                    <td class="px-6 py-3.5 text-sm text-gray-900 dark:text-gray-100 align-top">
                                        @if($isNewEntry)
                                            <span class="font-semibold">{{ \Carbon\Carbon::parse($detail->entry_date)->format('d/m/Y') }}</span>
                                            <div class="text-xs text-gray-400 mt-0.5" title="Description">{{ $detail->entry_description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-gray-900 dark:text-gray-100 align-top">
                                        <div class="{{ $detail->credit > 0 ? 'pl-8 text-gray-500 dark:text-gray-400' : 'font-medium' }}">
                                            {{ $detail->account_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-gray-500 dark:text-gray-400 align-top">
                                        {{ $detail->account_code }}
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-right font-medium align-top">
                                        @if($detail->debit > 0)
                                            <span class="text-gray-900 dark:text-white">Rp {{ number_format($detail->debit, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-700">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-right font-medium align-top">
                                        @if($detail->credit > 0)
                                            <span class="text-gray-500 dark:text-gray-400">Rp {{ number_format($detail->credit, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-700">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            <span class="font-medium">{{ __('finance.no_journal_entries', ['period' => \Carbon\Carbon::createFromFormat('Y-m', $filterMonth)->format('F Y')]) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($details->isNotEmpty())
                            <tfoot class="bg-gray-50 dark:bg-gray-800/40 border-t border-gray-200 dark:border-gray-800 text-sm font-bold">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-left text-gray-900 dark:text-white uppercase tracking-wider text-xs">{{ __('finance.total_summary') }}</td>
                                    <td class="px-6 py-4 text-right text-gray-900 dark:text-white">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-gray-900 dark:text-white">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::layout>
