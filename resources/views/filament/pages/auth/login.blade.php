
    <div class="flex h-screen w-full font-sans antialiased text-gray-900 bg-white dark:bg-gray-900 dark:text-white">
        <!-- Left Side: Branding / Background -->
        <div class="hidden lg:flex w-1/2 bg-blue-600 dark:bg-blue-900 items-center justify-center p-12 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-2xl opacity-50 dark:opacity-20"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-700 rounded-full mix-blend-multiply filter blur-2xl opacity-50 dark:opacity-20"></div>
            
            <div class="text-white max-w-lg text-center relative z-10">
                <svg class="w-24 h-24 mx-auto mb-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            <div>
                <x-text variant="title">Welcome to Admin Panel</x-text>
                <x-text variant="body" class="text-blue-100 dark:text-gray-300 text-lg mt-4">
                    Manage your application, view analytics, and control your system settings seamlessly.
                </x-text>
            </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full flex lg:w-1/2 items-center justify-center px-6 py-12 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-md w-full">
                <!-- Mobile heading fallback -->
                <div class="lg:hidden mb-10 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <x-text variant="title" class="tracking-tight">Welcome to Admin Panel</x-text>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                    <x-text variant="subtitle" class="mb-8 text-center">Sign In</x-text>
                    
                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}

                        <div class="mt-6">
                            <x-filament-panels::form.actions
                                :actions="$this->getCachedFormActions()"
                                :full-width="$this->hasFullWidthFormActions()"
                            />
                        </div>
                    </x-filament-panels::form>
                </div>
            </div>
        </div>
    </div>
