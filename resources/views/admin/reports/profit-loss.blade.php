<x-filament-panels::page>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <div x-data="{
        isOpen: false,
        loading: false,
        accountName: '',
        transactions: [],
        fetchDrilldown(accountId) {
            this.isOpen = true;
            this.loading = true;
            this.transactions = [];
            fetch('{{ route('reports.profit-loss.drilldown') }}?account_id=' + accountId + '&filter_type={{ $filter_type }}&filter_month={{ $filter_month }}&filter_year={{ $filter_year }}&start_date={{ $start_date_input }}&end_date={{ $end_date_input }}')
                .then(res => res.json())
                .then(data => {
                    this.accountName = data.account_name;
                    this.transactions = data.transactions;
                    this.loading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loading = false;
                });
        }
    }">
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ __('finance.profit_loss_statement') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('finance.pl_subtitle') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-button 
                        href="{{ route('reports.profit-loss.export-excel', request()->query()) }}" 
                        variant="success"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('finance.download_excel') }}
                    </x-button>
                    <x-button 
                        href="{{ route('reports.profit-loss.export-pdf', request()->query()) }}" 
                        variant="primary"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('finance.export_pdf') }}
                    </x-button>
                </div>
            </div>

            {{-- Filters Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5" x-data="{ filterType: '{{ $filter_type }}' }">
                <form method="GET" action="{{ route('reports.profit-loss') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        {{-- Filter Type selector --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.period_selection') }}</label>
                            <select 
                                name="filter_type" 
                                x-model="filterType"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                            >
                                <option value="monthly">{{ __('finance.monthly') }}</option>
                                <option value="yearly">{{ __('finance.yearly') }}</option>
                                <option value="custom">{{ __('finance.custom') }}</option>
                            </select>
                        </div>

                        {{-- Monthly Form Inputs --}}
                        <div x-show="filterType === 'monthly'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.select_month') }}</label>
                            <input 
                                type="month" 
                                name="filter_month" 
                                value="{{ $filter_month }}" 
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                            >
                        </div>

                        {{-- Yearly Form Inputs --}}
                        <div x-show="filterType === 'yearly'" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.select_year') }}</label>
                            <select 
                                name="filter_year" 
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                            >
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ $filter_year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Custom Date Inputs --}}
                        <div x-show="filterType === 'custom'" class="grid grid-cols-2 gap-2" x-cloak>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.start_date') }}</label>
                                <input 
                                    type="date" 
                                    name="start_date" 
                                    value="{{ $start_date_input }}" 
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.end_date') }}</label>
                                <input 
                                    type="date" 
                                    name="end_date" 
                                    value="{{ $end_date_input }}" 
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                                >
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-2">
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white font-medium rounded-lg text-sm transition"
                            >
                                {{ __('finance.search') }}
                            </button>
                            <a 
                                href="{{ route('reports.profit-loss') }}" 
                                class="flex-1 text-center px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                            >
                                {{ __('finance.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('finance.total_income') }}</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white whitespace-nowrap mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('finance.total_expenses') }}</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white whitespace-nowrap mt-0.5">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 {{ $netProfit >= 0 ? 'bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400' : 'bg-danger-50 dark:bg-danger-900/20 text-danger-600 dark:text-danger-400' }} rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('finance.profit_loss') }}</p>
                        <p class="text-xl font-black {{ $netProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }} whitespace-nowrap mt-0.5">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('finance.profit_margin') }}</p>
                        <p class="text-xl font-black text-indigo-600 dark:text-indigo-400 whitespace-nowrap mt-0.5">{{ number_format($profitMargin, 2, ',', '.') }}%</p>
                    </div>
                </div>
            </div>

            {{-- Trend Visualization Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">{{ __('finance.pl_trends', ['period' => $period_label]) }}</h3>
                <div class="h-80 w-full">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>

            {{-- Profit & Loss Dynamic Statement Table --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden p-6">
                <div class="text-center pb-6 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('finance.profit_loss_statement') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('finance.selected_period') ?? 'Selected Period' }}: {{ $period_label }}</p>
                </div>
                
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/40 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-800">
                                <th class="px-6 py-3 w-1/3">{{ __('finance.category_account') }}</th>
                                <th class="px-6 py-3 w-1/3">{{ __('finance.code') }}</th>
                                <th class="px-6 py-3 text-right w-1/3">{{ __('finance.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                            {{-- INCOME SECTION --}}
                            <tr class="bg-gray-50/20 dark:bg-gray-900/10">
                                <td colspan="3" class="px-6 py-3 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wide">{{ __('finance.income_category') }}</td>
                            </tr>
                            @forelse($revenueAccounts as $account)
                                <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/20 transition">
                                    <td class="px-6 py-3.5 text-sm text-gray-700 dark:text-gray-300 pl-12 font-medium">
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-gray-400">
                                        {{ $account->code }}
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-right align-middle">
                                        <button 
                                            type="button" 
                                            @click="fetchDrilldown({{ $account->id }})" 
                                            class="font-bold text-emerald-600 hover:text-emerald-500 border-b border-dashed border-emerald-300 hover:border-emerald-500 outline-none transition"
                                            title="{{ __('finance.click_to_view_details') }}"
                                        >
                                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-400 italic pl-12">{{ __('finance.no_income_accounts') }}</td>
                                </tr>
                            @endforelse
                            <tr class="bg-emerald-50/20 dark:bg-emerald-900/10 font-bold border-t border-gray-200 dark:border-gray-800">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white uppercase tracking-wider text-[11px] pl-8">{{ __('finance.total_income') }}</td>
                                <td></td>
                                <td class="px-6 py-4 text-sm text-right text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            </tr>

                            {{-- EXPENSES SECTION --}}
                            <tr class="bg-gray-50/20 dark:bg-gray-900/10">
                                <td colspan="3" class="px-6 py-3 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wide">{{ __('finance.expenses_category') }}</td>
                            </tr>
                            @forelse($expenseAccounts as $account)
                                <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/20 transition">
                                    <td class="px-6 py-3.5 text-sm text-gray-700 dark:text-gray-300 pl-12 font-medium">
                                        {{ $account->name }}
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-gray-400">
                                        {{ $account->code }}
                                    </td>
                                    <td class="px-6 py-3.5 text-sm text-right align-middle">
                                        <button 
                                            type="button" 
                                            @click="fetchDrilldown({{ $account->id }})" 
                                            class="font-bold text-rose-600 hover:text-rose-500 border-b border-dashed border-rose-300 hover:border-rose-500 outline-none transition"
                                            title="{{ __('finance.click_to_view_details') }}"
                                        >
                                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-400 italic pl-12">{{ __('finance.no_expense_accounts') }}</td>
                                </tr>
                            @endforelse
                            <tr class="bg-rose-50/20 dark:bg-rose-900/10 font-bold border-t border-gray-200 dark:border-gray-800">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white uppercase tracking-wider text-[11px] pl-8">{{ __('finance.total_expenses') }}</td>
                                <td></td>
                                <td class="px-6 py-4 text-sm text-right text-rose-600 dark:text-rose-400">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            </tr>

                            {{-- NET PROFIT SECTION --}}
                            <tr class="bg-gray-100/50 dark:bg-gray-800 font-black border-t-2 border-b-2 border-gray-900 dark:border-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white uppercase tracking-wider text-[11px]">{{ __('finance.net_profit_loss') }}</td>
                                <td></td>
                                <td class="px-6 py-4 text-sm text-right {{ $netProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Drilldown Modal --}}
        <div 
            x-show="isOpen" 
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
            x-cloak
            @keydown.escape.window="isOpen = false"
        >
            <div 
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-3xl w-full overflow-hidden"
                @click.away="isOpen = false"
            >
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-900 dark:text-white" x-text="'{{ __('finance.transactions_drilldown', ['account' => 'PLACEHOLDER']) }}'.replace('PLACEHOLDER', accountName)"></h3>
                    <button @click="isOpen = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    {{-- Loader --}}
                    <div x-show="loading" class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>

                    {{-- Transactions list --}}
                    <div x-show="!loading" class="overflow-x-auto max-h-[350px] overflow-y-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 uppercase text-[10px] font-bold">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">{{ __('finance.date') }}</th>
                                    <th class="px-4 py-2.5 text-left">{{ __('finance.description') }}</th>
                                    <th class="px-4 py-2.5 text-left">{{ __('finance.reference') }}</th>
                                    <th class="px-4 py-2.5 text-right">{{ __('finance.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <template x-for="trx in transactions">
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white" x-text="trx.date"></td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="trx.description"></td>
                                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400" x-text="trx.reference"></td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white" x-text="trx.amount"></td>
                                    </tr>
                                </template>
                                <tr x-show="transactions.length === 0">
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">{{ __('finance.no_transactions') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end bg-gray-50/50 dark:bg-gray-800/50">
                    <button 
                        @click="isOpen = false" 
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700"
                    >
                        {{ __('finance.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Line Trend scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('trendsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($trends['labels']),
                    datasets: [
                        {
                            label: '{{ __('finance.income_category') }}',
                            data: @json($trends['revenue']),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: '{{ __('finance.expenses_category') }}',
                            data: @json($trends['expense']),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: '{{ __('finance.profit_loss') }}',
                            data: @json($trends['profit']),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.05)',
                            borderWidth: 2.5,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-filament-panels::page>
