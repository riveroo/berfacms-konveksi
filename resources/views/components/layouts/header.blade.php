<!-- Header Component -->
<header x-data="{ mobileMenu: false }" 
    class="sticky top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 py-4 shadow-sm">

    <div class="container mx-auto px-6 flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center gap-2 group">
            @php $appearance = \App\Models\AppearanceSetting::first(); @endphp
            @if($appearance && $appearance->header_logo)
                <img src="{{ asset('storage/' . $appearance->header_logo) }}" alt="Konveksi Hub" class="h-10 w-auto">
            @else
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl transition-transform group-hover:scale-110 shadow-lg shadow-indigo-600/20">
                    K
                </div>
                <span class="font-outfit text-xl font-black tracking-tight transition-colors text-slate-900">
                    Konveksi <span class="text-indigo-500">hub</span>
                </span>
            @endif
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold tracking-wide uppercase">
            <a href="{{ url('/') }}#beranda" class="transition-colors hover:text-indigo-500 text-slate-600">Beranda</a>
            <a href="{{ url('/products') }}" class="transition-colors hover:text-indigo-500 text-slate-600">Produk Kami</a>
            <a href="{{ url('/') }}#tentang" class="transition-colors hover:text-indigo-500 text-slate-600">Our Values</a>
            <a href="#kontak" class="transition-colors hover:text-indigo-500 text-slate-600">Kontak</a>
        </nav>

        <div class="flex items-center gap-4">
            <x-button variant="indigo" href="{{ route('public.stock') }}"
                class="hidden sm:inline-flex rounded-full px-6 shadow-xl shadow-indigo-600/20">
                CEK STOCK
            </x-button>

            <button @click="mobileMenu = true" class="lg:hidden p-2 rounded-lg transition-colors text-slate-900 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div x-cloak x-show="mobileMenu" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"
        class="fixed inset-0 z-[60] bg-white lg:hidden flex flex-col p-8">
        <div class="flex items-center justify-between mb-12">
            @php $appearance = \App\Models\AppearanceSetting::first(); @endphp
            @if($appearance && $appearance->header_logo)
                <img src="{{ asset('storage/' . $appearance->header_logo) }}" alt="Konveksi Hub" class="h-10 w-auto">
            @else
                <span class="font-outfit text-2xl font-black tracking-tight">Konveksi <span
                        class="text-indigo-500">hub</span></span>
            @endif
            <button @click="mobileMenu = false" class="p-2 text-slate-400 hover:text-slate-900">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <nav class="flex flex-col gap-6 text-2xl font-bold font-outfit">
            <a href="{{ url('/') }}#beranda" @click="mobileMenu = false" class="hover:text-indigo-600">Beranda</a>
            <a href="{{ url('/products') }}" @click="mobileMenu = false" class="hover:text-indigo-600">Produk Kami</a>
            <a href="{{ url('/') }}#tentang" @click="mobileMenu = false" class="hover:text-indigo-600">Tentang Kami</a>
            <a href="#kontak" @click="mobileMenu = false" class="hover:text-indigo-600">Kontak</a>
        </nav>
        <div class="mt-auto">
            <x-button variant="indigo" href="{{ route('public.stock') }}" class="w-full py-4 rounded-2xl text-lg"
                @click="mobileMenu = false">
                CEK STOCK
            </x-button>
        </div>
    </div>
</header>