<x-filament-panels::page>
    {{-- Section 1: Supplier Information Card --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-6 space-y-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Supplier Information</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">General information and contact details of this supplier.</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 border-t border-gray-100 dark:border-gray-800 pt-6">
            <div>
                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Supplier Name</span>
                <span class="text-base font-bold text-gray-900 dark:text-white mt-1 block">{{ $this->record->name }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact</span>
                <span class="text-base font-bold text-gray-900 dark:text-white mt-1 block">{{ $this->record->contact ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</span>
                <span class="text-base font-medium text-gray-900 dark:text-white mt-1 block">{{ $this->record->address ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Information</span>
                <span class="text-base font-medium text-gray-900 dark:text-white mt-1 block">{{ $this->record->information ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Section 2 & 3: Supplier Item List Table --}}
    <div class="space-y-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Supplier Item List</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">List of items sourced from this supplier.</p>
        </div>
        
        {{ $this->table }}
    </div>
</x-filament-panels::page>
