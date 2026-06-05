<x-filament-panels::page>
    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('finance.accounting_period') }}</label>
                <input 
                    type="month" 
                    wire:model.live="period" 
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                >
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('finance.calculations_note') }}
            </div>
        </div>
    </div>

    {{-- Export Header --}}
    <div class="flex justify-between items-center gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('finance.trial_balance') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('finance.selected_period') }}: {{ $period_label }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(canAccessMenu('admin/import-export'))
                <a 
                    href="{{ route('admin.trial-balance.export-excel', ['period' => $period]) }}"
                    class="inline-flex items-center px-3 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('finance.export_excel') }}
                </a>

                <a 
                    href="{{ route('admin.trial-balance.export-pdf', ['period' => $period]) }}"
                    class="inline-flex items-center px-3 py-2 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition shadow-sm"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('finance.export_pdf') }}
                </a>
            @endif
        </div>
    </div>

    {{-- Validation Banner --}}
    <div class="p-4 rounded-xl border {{ $isBalanced ? 'bg-success-50/50 border-success-200 dark:bg-success-950/20 dark:border-success-900 text-success-800 dark:text-success-400' : 'bg-danger-50/50 border-danger-200 dark:bg-danger-950/20 dark:border-danger-900 text-danger-800 dark:text-danger-400' }} shadow-sm font-semibold text-sm">
        @if($isBalanced)
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>✅ {{ __('finance.trial_balance_balanced') }}</span>
            </div>
        @else
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>⚠ {{ __('finance.trial_balance_out_of_balance') }} ({{ __('finance.difference') }}: Rp {{ number_format($difference, 0, ',', '.') }})</span>
            </div>
        @endif
    </div>

    {{-- Report Table --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/40 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-800">
                        <th class="px-6 py-3.5">{{ __('finance.code') }}</th>
                        <th class="px-6 py-3.5">{{ __('finance.name') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('finance.debit') }}</th>
                        <th class="px-6 py-3.5 text-right">{{ __('finance.credit') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y-0">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition">
                            <td class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $row['code'] }}</td>
                            <td class="py-2 text-sm text-gray-900 dark:text-white {{ $row['parent_id'] ? 'pl-12' : 'pl-6' }} pr-6">{{ $row['name'] }}</td>
                            <td class="px-6 py-2 text-sm text-right text-gray-900 dark:text-white">
                                {{ $row['debit'] !== null ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-2 text-sm text-right text-gray-900 dark:text-white">
                                {{ $row['credit'] !== null ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                {{ __('finance.no_records') ?? 'No records found' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-bold border-t border-gray-400 dark:border-gray-600 border-b-4 border-double border-gray-400 dark:border-gray-600">
                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-white" colspan="2">{{ __('finance.total_summary') }}</td>
                        <td class="px-6 py-3 text-sm text-right text-gray-950 dark:text-white font-black">
                            Rp {{ number_format($totalDebit, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-gray-950 dark:text-white font-black">
                            Rp {{ number_format($totalCredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament-panels::page>
