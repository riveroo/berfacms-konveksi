<x-filament-panels::layout>
    <div class="flex items-center justify-center p-12 mt-10">
        <div class="text-center space-y-4">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <div>
                <x-text variant="title">Feature Coming Soon</x-text>
                <x-text variant="muted" class="mt-2">We are working hard to bring this feature to you.</x-text>
            </div>
            <div class="pt-4">
                <x-button variant="outline" href="{{ url('/admin') }}">
                    Back to Dashboard
                </x-button>
            </div>
        </div>
    </div>
</x-filament-panels::layout>
