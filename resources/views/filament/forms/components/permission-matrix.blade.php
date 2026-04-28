<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $state = $getState() ?? [];
            $grouped = collect($state)->groupBy('group');
        @endphp

        @foreach ($grouped as $group => $items)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-3 pb-2 border-b border-gray-100 dark:border-gray-800">
                    {{ $group }}
                </h3>
                <div class="space-y-3">
                    @foreach ($items as $itemIndex => $item)
                        @php
                            // Find the original index in the flat array to wire model correctly
                            $originalIndex = array_search($item, $state);
                        @endphp
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" 
                                wire:model="{{ $getStatePath() }}.{{ $originalIndex }}.can_access" 
                                class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 transition"
                            >
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition">
                                {{ $item['menu_name'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
