<x-filament-panels::layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ __('appearance.title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('appearance.subtitle') }}</p>
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
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('appearance.header_logo') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('appearance.header_logo_description') }}
                        </p>
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        @if($appearance->header_logo)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">{{ __('appearance.current_logo_preview') }}</span>
                                <img src="{{ asset('storage/' . $appearance->header_logo) }}" alt="Header Logo" class="h-12 w-auto object-contain">
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('appearance.upload_new_logo') }}</label>
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
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('appearance.favicon') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('appearance.favicon_description') }}
                        </p>
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        @if($appearance->favicon)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">{{ __('appearance.current_favicon_preview') }}</span>
                                <img src="{{ asset('storage/' . $appearance->favicon) }}" alt="Favicon" class="h-8 w-8 object-contain">
                            </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('appearance.upload_new_favicon') }}</label>
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

                <!-- Bank Account Information Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-gray-200 dark:border-gray-800 pt-8 mt-8">
                    <div class="md:col-span-1">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('appearance.bank_account_details') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('appearance.bank_account_description') }}
                        </p>
                    </div>
                    <div class="md:col-span-2 space-y-5">
                        {{-- Bank Logo --}}
                        <div class="space-y-2">
                            @if($appearance->bank_logo)
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">{{ __('appearance.current_bank_logo_preview') }}</span>
                                    <img src="{{ asset('storage/' . $appearance->bank_logo) }}" alt="Bank Logo" class="h-10 w-auto object-contain">
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('appearance.upload_bank_logo') }}</label>
                                <input type="file" name="bank_logo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                                    hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50
                                    cursor-pointer border border-gray-300 dark:border-gray-700 rounded-md
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- Account Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('appearance.account_number') }} (No Rekening)</label>
                            <input type="text" name="bank_account_number" value="{{ $appearance->bank_account_number }}" placeholder="e.g. 0561496870"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
                        </div>

                        {{-- Account Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('appearance.recipient_name') }} (Nama Penerima)</label>
                            <input type="text" name="bank_account_name" value="{{ $appearance->bank_account_name }}" placeholder="e.g. M Dwi Dzulqarnain Hambali"
                                class="w-full h-10 px-3 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        {{ __('appearance.save_settings') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-filament-panels::layout>
