<x-filament-panels::layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <x-text variant="title">Pre Order / RFQ List</x-text>
                <x-text variant="muted" class="mt-1">Manage all client pre-orders and quotations</x-text>
            </div>
            <x-button href="{{ route('pre-orders.create') }}" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Pre Order
            </x-button>
        </div>

        {{-- Filter Section --}}
        <form method="GET" action="{{ route('pre-orders.index') }}"
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Row 1: Search --}}
                <div>
                    <x-text variant="label" class="mb-1.5">Search (Name / Phone)</x-text>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Client name or phone..."
                        class="w-full h-10 px-4 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 focus:border-gray-900 dark:focus:ring-white/20 dark:focus:border-white outline-none transition">
                </div>

                {{-- Row 1: PO ID --}}
                <div>
                    <x-text variant="label" class="mb-1.5">PO ID</x-text>
                    <input type="text" name="po_id" value="{{ request('po_id') }}" placeholder="e.g. PO001"
                        class="w-full h-10 px-4 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 focus:border-gray-900 dark:focus:ring-white/20 dark:focus:border-white outline-none transition">
                </div>

                {{-- Row 1: Status Dropdown --}}
                <div>
                    <x-text variant="label" class="mb-1.5">Status</x-text>
                    <select name="status"
                        class="w-full h-10 px-4 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 focus:border-gray-900 dark:focus:ring-white/20 dark:focus:border-white outline-none transition cursor-pointer">
                        <option value="">All Status</option>
                        <option value="on process" {{ request('status') === 'on process' ? 'selected' : '' }}>On Process</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                {{-- Row 2: Date From --}}
                <div>
                    <x-text variant="label" class="mb-1.5">Date From</x-text>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full h-10 px-4 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 focus:border-gray-900 outline-none transition">
                </div>

                {{-- Row 2: Date To --}}
                <div>
                    <x-text variant="label" class="mb-1.5">Date To</x-text>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full h-10 px-4 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-900/20 focus:border-gray-900 outline-none transition">
                </div>

                {{-- Row 2: Action Buttons --}}
                <div class="flex items-end gap-2">
                    <x-button type="submit" variant="indigo" class="flex-1 h-10">
                        Apply
                    </x-button>
                    <x-button href="{{ route('pre-orders.index') }}" variant="outline" class="flex-1 h-10">
                        Reset
                    </x-button>
                </div>

            </div>
        </form>


        {{-- Table --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th
                                class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-10">
                                No</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                PO ID</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Client Name</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Phone Number</th>
                            <th
                                class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Date</th>
                            <th
                                class="px-4 py-4 text-right text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Total Price</th>
                            <th
                                class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Status</th>
                            <th
                                class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse ($preOrders as $i => $po)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                <td class="px-4 py-4 text-gray-400 dark:text-gray-500">
                                    {{ $preOrders->firstItem() + $i }}
                                </td>
                                <td class="px-4 py-4 font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ $po->po_id }}
                                </td>
                                <td class="px-4 py-4 text-gray-700 dark:text-gray-300">
                                    {{ optional($po->client)->client_name ?? '—' }}
                                </td>
                                <td class="px-4 py-4 text-gray-600 dark:text-gray-400">
                                    {{ optional($po->client)->phone_number ?? '—' }}
                                </td>
                                <td class="px-4 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ $po->created_at->format('d M Y') }}
                                </td>
                                <td
                                    class="px-4 py-4 text-right font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($po->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $statusClasses = match ($po->status) {
                                            'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                            'on process' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses }} whitespace-nowrap">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <x-button href="{{ route('pre-orders.detail', $po->id) }}" variant="outline" size="sm">
                                            Detail
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center text-gray-400 dark:text-gray-600">
                                    <svg class="mx-auto w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002-2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">No pre-orders found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($preOrders->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
                    {{ $preOrders->links() }}
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::layout>
