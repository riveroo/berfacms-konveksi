<!-- Client Logos (Infinite Scroll) -->
@php
    $section = \App\Models\LandingSection::where('key', 'client_logo')->first();
    $isActive = $section ? $section->is_active : true;
    $logos = \App\Models\LandingClientLogo::where('is_active', true)->orderBy('sort_order')->get();
@endphp

@if($isActive && $logos->count() > 0)
<section class="bg-white py-8 border-y border-slate-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 mb-8 text-center">
        <h3 class="font-bold text-slate-400 uppercase tracking-widest text-xs">Dipercaya Oleh Berbagai Perusahaan</h3>
    </div>

    <div class="relative flex overflow-x-hidden">
        <!-- First group -->
        <div class="animate-marquee flex items-center whitespace-nowrap">
            @foreach($logos as $logo)
                <div class="mx-10 md:mx-16 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300 w-32 md:w-40 h-16 md:h-20">
                    <img src="{{ asset('storage/' . $logo->image) }}" class="max-w-full max-h-full object-contain" alt="Client Logo">
                </div>
            @endforeach
            <!-- Repeat if less than 6 to ensure smooth loop -->
            @if($logos->count() < 6)
                @foreach($logos as $logo)
                    <div class="mx-10 md:mx-16 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300 w-32 md:w-40 h-16 md:h-20">
                        <img src="{{ asset('storage/' . $logo->image) }}" class="max-w-full max-h-full object-contain" alt="Client Logo">
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Second group (clone for seamless loop) -->
        <div class="absolute top-0 animate-marquee2 flex items-center whitespace-nowrap">
            @foreach($logos as $logo)
                <div class="mx-10 md:mx-16 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300 w-32 md:w-40 h-16 md:h-20">
                    <img src="{{ asset('storage/' . $logo->image) }}" class="max-w-full max-h-full object-contain" alt="Client Logo">
                </div>
            @endforeach
            <!-- Repeat if less than 6 to ensure smooth loop -->
            @if($logos->count() < 6)
                @foreach($logos as $logo)
                    <div class="mx-10 md:mx-16 flex items-center justify-center grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300 w-32 md:w-40 h-16 md:h-20">
                        <img src="{{ asset('storage/' . $logo->image) }}" class="max-w-full max-h-full object-contain" alt="Client Logo">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- Require CSS in the main layout for infinite marquee -->
<style>
    .animate-marquee {
        animation: marquee 30s linear infinite;
    }

    .animate-marquee2 {
        animation: marquee2 30s linear infinite;
    }

    @keyframes marquee {
        0% { transform: translateX(0%); }
        100% { transform: translateX(-100%); }
    }

    @keyframes marquee2 {
        0% { transform: translateX(100%); }
        100% { transform: translateX(0%); }
    }
</style>
@endif