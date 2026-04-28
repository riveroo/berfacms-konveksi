<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold">Menu</th>
                    <th scope="col" class="px-6 py-4 text-center font-bold">Read</th>
                    <th scope="col" class="px-6 py-4 text-center font-bold">Add</th>
                    <th scope="col" class="px-6 py-4 text-center font-bold">Edit</th>
                    <th scope="col" class="px-6 py-4 text-center font-bold">Delete</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($getState() ?? [] as $index => $row)
                    <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ str(str_replace('_', ' ', $row['menu_name']))->title() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" wire:model="{{ $getStatePath() }}.{{ $index }}.can_read" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition">
                        </td>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" wire:model="{{ $getStatePath() }}.{{ $index }}.can_add" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition">
                        </td>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" wire:model="{{ $getStatePath() }}.{{ $index }}.can_edit" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition">
                        </td>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" wire:model="{{ $getStatePath() }}.{{ $index }}.can_delete" class="w-5 h-5 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-dynamic-component>
