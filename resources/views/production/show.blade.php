<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0">
        <div class="space-y-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Production Details</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Reviewing production batch: {{ $production->batch_code }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-button href="{{ route('production.index') }}" variant="outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </x-button>
                </div>
            </div>

            {{-- Section 1: Information --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold">1</span>
                    General Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Production Date</label>
                        <input type="text" value="{{ $production->production_date->format('Y-m-d H:i') }}" readonly
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch Code</label>
                        <input type="text" value="{{ $production->batch_code }}" readonly
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-indigo-400 font-bold outline-none cursor-not-allowed">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Production Label / Name</label>
                        <input type="text" value="{{ $production->production_name }}" readonly
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                        <input type="text" value="{{ $production->user->name }}" readonly
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 outline-none cursor-not-allowed">
                    </div>
                </div>
            </div>

            {{-- Section 2: Material Consumption --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold">2</span>
                    Material Consumption (Stock Out)
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-2">Material</th>
                                <th class="px-4 py-2 w-32">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($production->materials as $mat)
                                <tr>
                                    <td class="px-2 py-3">
                                        <input type="text" value="{{ $mat->item->item_name }}" readonly
                                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 text-sm outline-none cursor-not-allowed">
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="text" value="{{ $mat->quantity }}" readonly
                                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 text-sm outline-none cursor-not-allowed">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Section 3: Product Output --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">3</span>
                    Product Output (Stock In)
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Variant</th>
                                <th class="px-4 py-2">Size</th>
                                <th class="px-4 py-2 w-32">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($production->products as $prod)
                                <tr>
                                    <td class="px-2 py-3">
                                        <input type="text" value="{{ $prod->product->product_name }}" readonly
                                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 text-sm outline-none cursor-not-allowed">
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="text" value="{{ $prod->variant->variant_name }}" readonly
                                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 text-sm outline-none cursor-not-allowed">
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="text" value="{{ $prod->sizeOption ? $prod->sizeOption->name : 'N/A' }}" readonly
                                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 text-sm outline-none cursor-not-allowed">
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="text" value="{{ $prod->quantity }}" readonly
                                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 text-sm outline-none cursor-not-allowed">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::layout>
