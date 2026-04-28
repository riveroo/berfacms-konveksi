@php
    $banner = \App\Models\LandingBannerCta::first();
@endphp

@if ($banner && $banner->is_active)
    <!-- Promotional Banner Section -->
    <section class="w-full bg-slate-50 py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-6">
            <a href="{{ $banner->link }}" class="block w-full rounded-2xl shadow-md overflow-hidden bg-slate-900 group relative flex flex-col md:flex-row items-center justify-between transition-transform duration-300 hover:shadow-xl hover:-translate-y-1">
                
                <!-- Left: Text Content -->
                <div class="relative z-10 px-8 py-10 md:py-14 md:px-12 w-full md:w-1/2 flex flex-col justify-center text-center md:text-left">
                    <h2 class="font-outfit text-3xl md:text-4xl font-black text-white mb-4 uppercase">{{ $banner->title }}</h2>
                    @if($banner->description)
                        <p class="text-indigo-100 text-lg">{{ $banner->description }}</p>
                    @endif
                </div>

                <!-- Right: Image Background (It sits behind on mobile, or right side on desktop depending on how we set it. Let's make it cover the right half or full background) -->
                <div class="absolute inset-0 w-full h-full">
                    <img src="{{ asset('storage/' . $banner->image) }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-60 transition-opacity duration-300" alt="{{ $banner->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t md:bg-gradient-to-r from-slate-900/95 via-slate-900/80 to-transparent"></div>
                </div>

            </a>
        </div>
    </section>
@endif