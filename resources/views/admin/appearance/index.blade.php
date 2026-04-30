<x-filament-panels::layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Appearance Settings</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage public branding assets like header logo and favicon.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/20 dark:text-red-400" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <form action="{{ route('admin.appearance.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
                @csrf

                <!-- Header Logo Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b border-gray-200 dark:border-gray-800 pb-8">
                    <div class="md:col-span-1">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Header Logo</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            This logo will be displayed on the public pages (e.g., Home, Products, Cart).
                            Recommended format: PNG with transparent background.
                        </p>
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        @if($appearance->header_logo)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Current Logo Preview</span>
                                <img src="{{ asset('storage/' . $appearance->header_logo) }}" alt="Header Logo" class="h-12 w-auto object-contain">
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload New Logo</label>
                            <input type="file" name="header_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50
                                cursor-pointer border border-gray-300 dark:border-gray-700 rounded-md
                                focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Favicon Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Favicon</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            The small icon shown in the browser tab.
                            Recommended size: 32x32 or 64x64. Format: .ico or .png.
                        </p>
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        @if($appearance->favicon)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Current Favicon Preview</span>
                                <img src="{{ asset('storage/' . $appearance->favicon) }}" alt="Favicon" class="h-8 w-8 object-contain">
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload New Favicon</label>
                            <input type="file" name="favicon" accept=".ico,.png" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50
                                cursor-pointer border border-gray-300 dark:border-gray-700 rounded-md
                                focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Save Appearance Settings
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-filament-panels::layout>
