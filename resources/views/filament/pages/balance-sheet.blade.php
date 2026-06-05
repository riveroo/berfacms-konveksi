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
            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('finance.balance_sheet_statement') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('finance.selected_period') ?? 'Selected Period' }}: {{ $period_label }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a 
                href="{{ route('admin.balance-sheet.export-excel', ['period' => $period]) }}"
                class="inline-flex items-center px-3 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('finance.export_excel') }}
            </a>

            <a 
                href="{{ route('admin.balance-sheet.export-pdf', ['period' => $period]) }}"
                class="inline-flex items-center px-3 py-2 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition shadow-sm"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('finance.export_pdf') }}
            </a>
        </div>
    </div>

    {{-- Validation Banner --}}
    <div class="p-4 rounded-xl border {{ $isBalanced ? 'bg-success-50/50 border-success-200 dark:bg-success-950/20 dark:border-success-900 text-success-800 dark:text-success-400' : 'bg-danger-50/50 border-danger-200 dark:bg-danger-950/20 dark:border-danger-900 text-danger-800 dark:text-danger-400' }} shadow-sm font-semibold text-sm">
        @if($isBalanced)
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ __('finance.balance_sheet_balanced') }}</span>
            </div>
        @else
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ __('finance.balance_sheet_unbalanced', ['difference' => number_format($difference, 0, ',', '.')]) }}</span>
            </div>
        @endif
    </div>

    {{-- Report Content Table with Collapsible Sections --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm overflow-hidden p-6 space-y-6">
        <div class="w-full text-left">
            {{-- HEADER ROW --}}
            <div class="grid grid-cols-3 bg-gray-50/50 dark:bg-gray-800/40 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-800 px-6 py-3">
                <div class="col-span-2">{{ __('finance.category_account') }}</div>
                <div class="text-right">{{ __('finance.balance') }}</div>
            </div>

            {{-- 1. ASSETS SECTION (Collapsible) --}}
            <div x-data="{ openAssets: true }" class="border-b border-gray-100 dark:border-gray-800">
                <div @click="openAssets = !openAssets" class="flex justify-between items-center cursor-pointer bg-gray-50/20 dark:bg-gray-800/10 px-6 py-3.5 hover:bg-gray-50/40 dark:hover:bg-gray-800/20 transition">
                    <div class="flex items-center gap-2 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wide">
                        <span x-text="openAssets ? '▼' : '▶'" class="text-[10px] text-gray-400"></span>
                        <span>{{ __('finance.assets') }}</span>
                    </div>
                    <div class="text-right font-black text-sm text-gray-950 dark:text-white">
                        Rp {{ number_format($totalAssets, 0, ',', '.') }}
                    </div>
                </div>

                <div x-show="openAssets" x-collapse class="pl-4 divide-y divide-gray-100 dark:divide-gray-800/50">
                    {{-- 1.1 Current Assets --}}
                    <div x-data="{ openCA: true }" class="py-2">
                        <div @click="openCA = !openCA" class="flex justify-between items-center cursor-pointer px-4 py-2 hover:bg-gray-50/30 dark:hover:bg-gray-800/10 rounded-lg">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                <span x-text="openCA ? '▼' : '▶'" class="text-[8px] text-gray-400"></span>
                                {{ __('finance.current_assets') }}
                            </span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($totalCurrentAssets, 0, ',', '.') }}
                            </span>
                        </div>

                        <div x-show="openCA" x-collapse class="pl-4 pr-4 space-y-1 mt-1">
                            {{-- Cash --}}
                            <div class="pl-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.cash_equivalents') }}</span>
                                    <span>Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @foreach($cashAccounts as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Accounts Receivable --}}
                            <div class="pl-2 mt-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.accounts_receivable') }}</span>
                                    <span>Rp {{ number_format($totalAR, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @forelse($arAccounts as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 italic py-0.5 pl-2">{{ __('finance.no_ar_accounts') }}</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Inventory --}}
                            <div class="pl-2 mt-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.inventory') }}</span>
                                    <span>Rp {{ number_format($totalInventory, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @foreach($inventoryAccounts as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 1.2 Non-Current Assets --}}
                    <div x-data="{ openNCA: true }" class="py-2">
                        <div @click="openNCA = !openNCA" class="flex justify-between items-center cursor-pointer px-4 py-2 hover:bg-gray-50/30 dark:hover:bg-gray-800/10 rounded-lg">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                <span x-text="openNCA ? '▼' : '▶'" class="text-[8px] text-gray-400"></span>
                                {{ __('finance.non_current_assets') }}
                            </span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($totalNonCurrentAssets, 0, ',', '.') }}
                            </span>
                        </div>

                        <div x-show="openNCA" x-collapse class="pl-4 pr-4 space-y-1 mt-1">
                            {{-- PPE --}}
                            <div class="pl-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.ppe') }}</span>
                                    <span>Rp {{ number_format($totalPPE, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @forelse($ppeAccounts as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 italic py-0.5 pl-2">{{ __('finance.no_fixed_assets') }}</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Accumulated Depreciation --}}
                            <div class="pl-2 mt-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5 text-rose-600 dark:text-rose-400">
                                    <span>{{ __('finance.accumulated_depreciation') }}</span>
                                    <span>(Rp {{ number_format($totalDepreciation, 0, ',', '.') }})</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @forelse($depreciationAccounts as $acc)
                                        <div class="flex justify-between text-xs text-rose-500/80 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 italic py-0.5 pl-2">{{ __('finance.no_depr_accounts') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. LIABILITIES & EQUITY SECTION (Collapsible) --}}
            <div x-data="{ openLiabilities: true }" class="border-b border-gray-100 dark:border-gray-800 pt-2">
                <div @click="openLiabilities = !openLiabilities" class="flex justify-between items-center cursor-pointer bg-gray-50/20 dark:bg-gray-800/10 px-6 py-3.5 hover:bg-gray-50/40 dark:hover:bg-gray-800/20 transition">
                    <div class="flex items-center gap-2 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wide">
                        <span x-text="openLiabilities ? '▼' : '▶'" class="text-[10px] text-gray-400"></span>
                        <span>{{ __('finance.liabilities_equity') }}</span>
                    </div>
                    <div class="text-right font-black text-sm text-gray-950 dark:text-white">
                        Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}
                    </div>
                </div>

                <div x-show="openLiabilities" x-collapse class="pl-4 divide-y divide-gray-100 dark:divide-gray-800/50">
                    {{-- 2.1 Current Liabilities --}}
                    <div x-data="{ openCL: true }" class="py-2">
                        <div @click="openCL = !openCL" class="flex justify-between items-center cursor-pointer px-4 py-2 hover:bg-gray-50/30 dark:hover:bg-gray-800/10 rounded-lg">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                <span x-text="openCL ? '▼' : '▶'" class="text-[8px] text-gray-400"></span>
                                {{ __('finance.current_liabilities') }}
                            </span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($totalCurrentLiabilities, 0, ',', '.') }}
                            </span>
                        </div>

                        <div x-show="openCL" x-collapse class="pl-4 pr-4 space-y-1 mt-1">
                            {{-- Accounts Payable --}}
                            <div class="pl-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.accounts_payable') }}</span>
                                    <span>Rp {{ number_format($totalAP, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @forelse($apAccounts as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 italic py-0.5 pl-2">{{ __('finance.no_ap_accounts') }}</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Accrued Expenses --}}
                            <div class="pl-2 mt-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.accrued_expenses') }}</span>
                                    <span>Rp {{ number_format($totalAccrued, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @forelse($accruedLiabilities as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 italic py-0.5 pl-2">{{ __('finance.no_accrued_expenses') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2.2 Equity --}}
                    <div x-data="{ openEQ: true }" class="py-2">
                        <div @click="openEQ = !openEQ" class="flex justify-between items-center cursor-pointer px-4 py-2 hover:bg-gray-50/30 dark:hover:bg-gray-800/10 rounded-lg">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                <span x-text="openEQ ? '▼' : '▶'" class="text-[8px] text-gray-400"></span>
                                {{ __('finance.equity') }}
                            </span>
                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($totalEquity, 0, ',', '.') }}
                            </span>
                        </div>

                        <div x-show="openEQ" x-collapse class="pl-4 pr-4 space-y-1 mt-1">
                            {{-- Share Capital --}}
                            <div class="pl-2">
                                <div class="flex justify-between text-xs text-gray-500 font-semibold mb-0.5">
                                    <span>{{ __('finance.share_capital') }}</span>
                                    <span>Rp {{ number_format($totalShareCapital, 0, ',', '.') }}</span>
                                </div>
                                <div class="pl-4 border-l border-gray-100 dark:border-gray-800 space-y-1">
                                    @forelse($equityAccounts as $acc)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 py-0.5">
                                            <span>{{ $acc->code }} - {{ $acc->name }}</span>
                                            <span>Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-[10px] text-gray-400 italic py-0.5 pl-2">{{ __('finance.no_capital_accounts') }}</div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Retained Earnings --}}
                            <div class="pl-2 mt-2">
                                <div class="flex justify-between text-xs text-gray-900 dark:text-white font-bold py-0.5">
                                    <span>{{ __('finance.retained_earnings') }}</span>
                                    <span>Rp {{ number_format($retainedEarnings, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
