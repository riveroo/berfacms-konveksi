<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Produk - KonveksiHub</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="antialiased font-sans bg-gray-50 text-gray-900 flex flex-col min-h-screen">
    <x-layouts.header />

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6 pt-18 mb-12 flex-grow w-full"
        x-data="{ mobileFiltersOpen: false }">
        <nav class="flex items-center text-sm text-gray-500 mb-6 font-medium">
            <a href="/" class="hover:text-indigo-600 transition-colors">Home</a>
            <span class="mx-2">›</span>
            <span class="text-gray-900 font-bold">Semua Produk</span>
        </nav>

        <div class="flex flex-col gap-8 relative items-start">
            <div class="flex-1 w-full min-w-0">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-2 sm:p-4 sm:px-5 rounded-2xl shadow-sm border border-gray-200 mb-6 gap-4">
                    <div class="hidden sm:block text-gray-800 font-extrabold text-lg">
                        Menampilkan Produk
                    </div>

                    <div
                        class="flex items-center justify-end sm:justify-start gap-3 w-full sm:w-auto mt-2 sm:mt-0 px-2 pb-2 sm:p-0 ml-auto">
                        <span class="text-sm font-medium text-gray-500 hidden md:block">Urutkan:</span>
                        <div class="relative w-full sm:w-auto min-w-[160px]">
                            <select onchange="window.location.href='?sort='+this.value"
                                class="appearance-none bg-gray-50 border border-gray-200 text-gray-800 font-medium text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full outline-none py-2.5 pl-4 pr-10 shadow-sm cursor-pointer hover:bg-gray-100 transition-colors">
                                <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Paling Sesuai</option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga Terendah
                                </option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid Ecosystem running Async Loading State Bindings -->
                <div x-data="{ gridLoading: true }" x-init="setTimeout(() => gridLoading = false, 800)">

                    <!-- 1. Skeleton Loaders (Active natively during 800ms loading phase) -->
                    <div x-show="gridLoading"
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        @for ($i = 0; $i < 8; $i++)
                            <div
                                class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden flex flex-col h-full animate-pulse border-opacity-50">
                                <div class="w-full aspect-[1/1] bg-gray-200 shrink-0"></div>
                                <div class="p-4 flex flex-col flex-1 gap-3 bg-white">
                                    <div class="h-4 bg-gray-200 rounded w-4/5 mb-1"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>

                                    <div class="mt-auto flex flex-col gap-4 pt-4">
                                        <div class="h-5 bg-gray-200 rounded w-1/3 mt-2"></div>
                                        <div class="h-9 bg-gray-200 rounded-lg w-full"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <!-- 2. Actual Product Grid (Seamless transition after data resolves) -->
                    <div x-cloak x-show="!gridLoading" style="display: none;"
                        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        @forelse($products as $product)
                            <!-- Refined Standard Native Hover Scaling via transition-all duration-300 ease-in-out hover:-translate-y-1 hover:shadow-lg hover:border-indigo-200 class chain! -->
                            <a href="{{ route('products.show', $product->id) }}"
                                class="group bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:border-indigo-200 flex flex-col h-full relative">

                                <div class="w-full aspect-[1/1] bg-gray-100 overflow-hidden relative shrink-0">
                                    @if($product->thumbnail)
                                        <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->product_name }}"
                                            loading="lazy"
                                            class="w-full h-full object-cover object-center bg-gray-50 mix-blend-multiply transition-transform duration-300 ease-in-out group-hover:scale-105">
                                    @else
                                        <img src="https://placehold.co/400x400/f3f4f6/94a3b8?text=No+Image" alt="Placeholder"
                                            loading="lazy"
                                            class="w-full h-full object-cover object-center bg-gray-50 mix-blend-multiply transition-transform duration-300 ease-in-out group-hover:scale-105">
                                    @endif

                                    <div
                                        class="absolute top-2 left-2 flex flex-col gap-1 items-start pointer-events-none z-10">
                                        @if(rand(0, 1))
                                            <span
                                                class="bg-indigo-600 text-white text-[10.5px] font-bold tracking-wide px-2.5 py-1 rounded-md shadow-sm pointer-events-auto opacity-95">
                                                Ekstra Voucher
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-4 flex flex-col flex-1 bg-white relative z-20">
                                    <h3
                                        class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug mb-2 transition-colors group-hover:text-indigo-600">
                                        {{ $product->product_name }}
                                    </h3>

                                    <div class="mt-auto flex flex-col gap-3">
                                        @php
                                            $prices = $product->variants->flatMap->stocks->pluck('price')->filter()->toArray();
                                            $minPrice = count($prices) > 0 ? min($prices) : 0;
                                            $maxPrice = count($prices) > 0 ? max($prices) : 0;
                                        @endphp

                                        <div class="text-base sm:text-lg font-extrabold text-gray-900 truncate">
                                            @if($minPrice == 0 && $maxPrice == 0)
                                                <span class="text-gray-400 font-medium text-sm">Harga tidak tersedia</span>
                                            @elseif($minPrice == $maxPrice)
                                                Rp{{ number_format($minPrice, 0, ',', '.') }}
                                            @else
                                                Rp{{ number_format($minPrice, 0, ',', '.') }} -
                                                Rp{{ number_format($maxPrice, 0, ',', '.') }}
                                            @endif
                                        </div>

                                        <div
                                            class="w-full py-2 rounded-lg bg-indigo-50 group-hover:bg-indigo-600 group-hover:text-white border border-indigo-100 text-indigo-600 text-xs font-bold text-center transition-all">
                                            Lihat Detail
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div
                                class="col-span-full py-20 text-center bg-white rounded-2xl shadow-sm border border-gray-200">
                                <svg class="mx-auto h-20 w-20 text-gray-300 mb-6 drop-shadow-sm" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Tidak Ada Produk</h3>
                                <p class="text-gray-500 mt-2 text-base max-w-sm mx-auto">Kami tidak dapat menemukan produk
                                    yang sesuai dengan kriteria Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>

    <x-layouts.footer />
</body>

</html>