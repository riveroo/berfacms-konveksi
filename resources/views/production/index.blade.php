<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-6">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Production <span class="text-xs font-normal text-gray-400">(v2)</span></h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and monitor production batches</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-button href="{{ route('production.create') }}" variant="primary" class="w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Add Production
                    </x-button>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filter Section --}}
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm w-full">
                <form method="GET" action="{{ route('production.index') }}"
                    class="flex flex-row items-end gap-6 w-full">
                    {{-- User Filter --}}
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 ml-1">User</label>
                        <select name="user_id"
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none cursor-pointer">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Filter --}}
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 ml-1">Production
                            Date</label>
                        <div class="flex items-center gap-1">
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                class="w-full h-10 px-1.5 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-[10px] sm:text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                            <span class="text-gray-400">-</span>
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                class="w-full h-10 px-1.5 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white text-[10px] sm:text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="flex-1 min-w-0">
                        <label
                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 ml-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Name or Batch..."
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-2 w-48 shrink-0">
                        <button type="submit"
                            class="flex-1 h-10 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Search
                        </button>
                        <a href="{{ route('production.index') }}"
                            class="flex-1 h-10 px-4 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Section --}}
            <div
                class="bg-white dark:bg-gray-900 shadow-sm rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    No</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Prod Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Batch Code</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Production Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Materials</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Products</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    User</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse($productions as $index => $prod)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $productions->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $prod->production_date->format('Y-m-d H:i') }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $prod->batch_code }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $prod->production_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $prod->materials->count() }} items
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                        {{ $prod->products->count() }} items
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $prod->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('production.show', $prod->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        No production records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination & Show Per Page --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
                            <form method="GET" action="{{ route('production.index') }}" id="perPageForm">
                                @foreach(request()->except('perPage') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="perPage" onchange="document.getElementById('perPageForm').submit()"
                                    class="h-9 w-16 px-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                                    <option value="10" {{ request('perPage') == '10' ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('perPage') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('perPage') == '50' ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                            <span class="text-sm text-gray-600 dark:text-gray-400">results</span>
                        </div>

                        <div class="production-pagination">
                            {{ $productions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .production-pagination nav>div:last-child {
            @apply flex items-center gap-2 !important;
        }

        .production-pagination nav span[aria-current="page"]>span {
            @apply bg-indigo-600 text-white border-indigo-600 !important;
        }

        .production-pagination nav a,
        .production-pagination nav span {
            @apply px-3 py-1 rounded-md border border-gray-300 dark:border-gray-700 transition-colors !important;
        }
    </style>
</x-filament-panels::layout>