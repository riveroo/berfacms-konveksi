<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">
            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Transactions Dashboard</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Analyze revenue metrics, sales trends, top-selling products, and top spenders.</p>
                </div>
            </div>

            {{-- Date Filters Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-4">
                <form method="GET" action="{{ route('transactions.report') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            value="{{ $start_date }}" 
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            value="{{ $end_date }}" 
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-white"
                        >
                    </div>
                    <div class="flex gap-2">
                        <button 
                            type="submit" 
                            class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white font-medium rounded-lg text-sm transition"
                        >
                            Filter
                        </button>
                        <a 
                            href="{{ route('transactions.report') }}" 
                            class="flex-1 text-center px-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Summary Metrics --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Revenue</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white whitespace-nowrap mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Products Sold</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white whitespace-nowrap mt-0.5">{{ number_format($totalProductsSold) }} units</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Average Order Value</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white whitespace-nowrap mt-0.5">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Daily Trend Chart Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Daily Sales Trend</h3>
                <div class="h-80 w-full">
                    <canvas id="salesTrendsChart"></canvas>
                </div>
            </div>

            {{-- Ranking Reports Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Top-Selling Products Table --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Top-Selling Products (Top 5)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 uppercase text-[10px] font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-left">Product</th>
                                    <th class="px-6 py-3 text-center w-24">Qty Sold</th>
                                    <th class="px-6 py-3 text-right w-40">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($topProducts as $item)
                                    <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/20 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($item->product && $item->product->thumbnail)
                                                    <img src="{{ Storage::url($item->product->thumbnail) }}" class="w-8 h-8 rounded-lg object-cover bg-gray-100 border border-gray-200">
                                                @else
                                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center text-xs text-gray-400 font-bold">P</div>
                                                @endif
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->product->product_name ?? 'Unknown Product' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-gray-600 dark:text-gray-400">
                                            {{ number_format($item->total_qty) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-indigo-600 dark:text-indigo-400">
                                            Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">No sales recorded for this period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Top Customer Spenders Table --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Top Spenders (Top 5 Customers)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 uppercase text-[10px] font-bold">
                                <tr>
                                    <th class="px-6 py-3 text-left">Customer</th>
                                    <th class="px-6 py-3 text-center w-24">Orders Count</th>
                                    <th class="px-6 py-3 text-right w-40">Total Spending</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($topCustomers as $cust)
                                    <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/20 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $cust->client->client_name ?? 'Guest' }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $cust->client->phone_number ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-gray-600 dark:text-gray-400">
                                            {{ number_format($cust->total_orders) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-rose-600 dark:text-rose-400">
                                            Rp {{ number_format($cust->total_spending, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">No customer orders recorded for this period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Line Trend scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesTrendsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [
                        {
                            label: 'Sales Revenue',
                            data: @json($trendData),
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
</x-filament-panels::layout>
