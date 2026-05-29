<x-filament-panels::page>
    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 mb-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Select Account</label>
                <select 
                    wire:model.live="accountId" 
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                >
                    <option value="">Choose Account...</option>
                    @foreach($this->getAccountsProperty() as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Accounting Period</label>
                <input 
                    type="month" 
                    wire:model.live="period" 
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                >
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    @if(!$accountId)
        <div class="flex flex-col items-center justify-center p-12 text-center bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm">
            <div class="p-3 bg-gray-50 dark:bg-gray-800/40 text-gray-400 rounded-full mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No Account Selected</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm">Please select an account to generate the ledger report.</p>
        </div>
    @else
        <div class="space-y-4">
            <div class="flex justify-between items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Ledger Records</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Showing posted transactions from journal entries.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a 
                        href="{{ route('admin.general-ledger.export-excel', ['account_id' => $accountId, 'period' => $period]) }}"
                        class="inline-flex items-center px-3 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </a>

                    <a 
                        href="{{ route('admin.general-ledger.export-pdf', ['account_id' => $accountId, 'period' => $period]) }}"
                        class="inline-flex items-center px-3 py-2 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>

            <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-900 p-1">
                {{ $this->table }}
            </div>
        </div>
    @endif
</x-filament-panels::page>
