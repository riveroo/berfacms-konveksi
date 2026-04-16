<x-filament-panels::layout>
    <div class="space-y-6">
        {{-- Header & Global Filters --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <x-text variant="title">Sales Report</x-text>
                <x-text variant="muted" class="mt-1">Analyze your business performance and revenue</x-text>
            </div>
            
            <form method="GET" action="{{ route('transactions.report') }}" class="flex flex-wrap items-end gap-3 bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div>
                    <x-text variant="label" class="mb-1">Start Date</x-text>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="h-9 px-3 text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 outline-none transition">
                </div>
                <div>
                    <x-text variant="label" class="mb-1">End Date</x-text>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="h-9 px-3 text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 outline-none transition">
                </div>
                <div>
                    <x-text variant="label" class="mb-1">Status</x-text>
                    <select name="status" class="h-9 px-3 text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 outline-none transition">
                        <option value="">All Status</option>
                        <option value="waiting for payment" {{ $status == 'waiting for payment' ? 'selected' : '' }}>Waiting for Payment</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="on progress" {{ $status == 'on progress' ? 'selected' : '' }}>On Progress</option>
                        <option value="done" {{ $status == 'done' ? 'selected' : '' }}>Done</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="indigo" size="sm" class="h-9 px-6">Filter</x-button>
                    <x-button href="{{ route('transactions.report') }}" variant="outline" size="sm" class="h-9">Reset</x-button>
                </div>
            </form>
        </div>

        {{-- Overview Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($overview['total_orders']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white whitespace-nowrap">Rp {{ number_format($overview['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Customers</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($overview['total_customers']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Order Value</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white whitespace-nowrap">Rp {{ number_format($overview['avg_order_value'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Daily Stats --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Daily Sales Summary</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-center">Orders</th>
                                <th class="px-6 py-3 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($dailyStats as $stat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ Carbon\Carbon::parse($stat->date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $stat->total_orders }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($stat->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">No data available for this range</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Product Performance --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Product Performance (Top 10)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-6 py-3 text-left">Product</th>
                                <th class="px-6 py-3 text-center">Qty Sold</th>
                                <th class="px-6 py-3 text-right">Raw Earnings</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($topProducts as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->thumbnail)
                                                <img src="{{ Storage::url($item->product->thumbnail) }}" class="w-8 h-8 rounded-lg object-cover bg-gray-100 border border-gray-200">
                                            @endif
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $item->product->product_name ?? 'Unknown Product' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-600 dark:text-gray-400">{{ number_format($item->total_qty) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">No sales data recorded</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Customer Report --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col lg:col-span-2">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Customer Spending Report (Top 10)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-6 py-3 text-left">Customer Name</th>
                                <th class="px-6 py-3 text-left">Phone Number</th>
                                <th class="px-6 py-3 text-center">Orders Count</th>
                                <th class="px-6 py-3 text-right">Total Spending</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($topCustomers as $cust)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $cust->client->client_name ?? 'Guest' }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $cust->client->phone_number ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $cust->total_orders }}</td>
                                    <td class="px-6 py-4 text-right font-black text-rose-600 dark:text-rose-400">Rp {{ number_format($cust->total_spending, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">No customer data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::layout>
