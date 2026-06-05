<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ __('finance.cash_book') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('finance.manage_cash_transactions') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-button href="{{ route('cash-book.create') }}" variant="primary">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                         </svg>
                         {{ __('finance.record_transaction') }}
                    </x-button>
                </div>
            </div>

            {{-- Summary Widget --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('finance.total_money_in') }}</p>
                        <h3 class="text-2xl font-bold text-success-600 mt-1">Rp {{ number_format($totalIn, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 bg-success-50 dark:bg-success-900/20 rounded-full">
                        <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('finance.total_money_out') }}</p>
                        <h3 class="text-2xl font-bold text-danger-600 mt-1">Rp {{ number_format($totalOut, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/20 rounded-full">
                         <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('finance.balance') }}</p>
                        <h3 class="text-2xl font-bold {{ $balance >= 0 ? 'text-primary-600' : 'text-danger-600' }} mt-1">Rp {{ number_format($balance, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-full">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-4">
                <form method="GET" action="{{ route('cash-book.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.from_account') }}</label>
                        <select name="account_id" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">{{ __('finance.all_cash_bank_accounts') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.start_date') }}</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.end_date') }}</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <input type="text" name="search" placeholder="{{ __('finance.search_desc') }}" value="{{ request('search') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-200">{{ __('finance.filter') }}</button>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('finance.no') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('finance.date') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('finance.description') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-right text-success-600">{{ __('finance.debit') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-right text-danger-600">{{ __('finance.credit') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('finance.from_account') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('finance.category') }}</th>
                                <th class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('finance.customer') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 whitespace-nowrap">
                            @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-75">
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $tx->date->format('d M Y') }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300 truncate max-w-xs">
                                    <a href="{{ route('cash-book.show', $tx->id) }}" class="text-primary-600 hover:underline">
                                        {{ $tx->description }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 text-sm text-right font-medium text-success-600">
                                    {{ in_array($tx->type, ['in', 'money_in', 'transfer']) ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-right font-medium text-danger-600">
                                    {{ in_array($tx->type, ['out', 'money_out', 'transfer']) ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ $tx->account->name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $tx->counterAccount->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $tx->client->client_name ?? '-' }}
                                        </div>
                                        @if($tx->receive_from)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $tx->receive_from }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    {{ __('finance.no_transactions_found') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Showing 
                        <span class="font-medium">{{ $transactions->firstItem() ?? 0 }}</span> 
                        to 
                        <span class="font-medium">{{ $transactions->lastItem() ?? 0 }}</span> 
                        of 
                        <span class="font-medium">{{ $transactions->total() }}</span> 
                        entries
                    </div>
                    <div>
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::layout>
