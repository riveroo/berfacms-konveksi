<div class="space-y-6" x-data="{ openHarga: true }">
    <!-- Filter Harga -->
    <div class="pb-2">
        <button @click="openHarga = !openHarga" class="flex justify-between items-center w-full text-left font-bold text-gray-800 mb-2">
            <span>Rentang Harga</span>
            <svg class="w-4 h-4 transform transition-transform" :class="{'rotate-180': openHarga}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
        </button>
        <div x-show="openHarga" class="mt-4 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Harga Minimum</label>
                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition-all shadow-sm">
                    <span class="text-gray-500 font-bold mr-2 select-none">Rp</span>
                    <input type="number" placeholder="0" min="0" class="w-full bg-transparent border-none p-0 focus:ring-0 text-gray-900 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Harga Maksimum</label>
                <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition-all shadow-sm">
                    <span class="text-gray-500 font-bold mr-2 select-none">Rp</span>
                    <input type="number" placeholder="1.000.000" min="0" class="w-full bg-transparent border-none p-0 focus:ring-0 text-gray-900 outline-none">
                </div>
            </div>
        </div>
    </div>
</div>
