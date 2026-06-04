<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->product_name }} - KonveksiHub</title>
    @php $appearance = \App\Models\AppearanceSetting::first(); @endphp
    <link rel="icon" type="image/png"
        href="{{ $appearance && $appearance->favicon ? asset('storage/' . $appearance->favicon) : asset('images/favicon.png') }}">
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

        .thumb-scroll::-webkit-scrollbar {
            height: 0;
            display: none;
        }

        .thumb-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased font-sans bg-white text-gray-900 min-h-screen flex flex-col">

    <x-layouts.header />

    <!-- Breadcrumb -->
    <div class="py-4 text-center text-sm max-w-5xl mx-auto px-4 sm:px-6">
        <nav class="text-xs text-gray-400 flex flex-wrap items-center justify-center gap-1">
            <a href="/" class="hover:text-indigo-600">Home</a>
            <span>›</span>
            <a href="{{ route('products.index') }}" class="hover:text-indigo-600">Katalog</a>
            <span>›</span>
            <span class="text-gray-600 truncate max-w-[180px] break-words">{{ $product->product_name }}</span>
        </nav>
    </div>

    @php
        $requestedVariantId = request()->query('variant_id');
        $defaultVariant = null;
        $defaultSizeId = null;

        if ($requestedVariantId) {
            $defaultVariant = $product->variants->firstWhere('id', $requestedVariantId);
            if ($defaultVariant) {
                // Select a random size from those with stock > 0
                $availableStocks = $defaultVariant->stocks->where('stock', '>', 0);
                if ($availableStocks->isNotEmpty()) {
                    $defaultSizeId = $availableStocks->random()->size_option_id;
                } else {
                    $anyStock = $defaultVariant->stocks;
                    if ($anyStock->isNotEmpty()) {
                        $defaultSizeId = $anyStock->random()->size_option_id;
                    }
                }
            }
        }

        if (!$defaultVariant) {
            $defaultVariant = $product->variants->count() > 0 ? $product->variants->random() : null;
            if ($defaultVariant && $defaultVariant->stocks->isNotEmpty()) {
                $availableStocks = $defaultVariant->stocks->where('stock', '>', 0);
                $defaultSizeId = $availableStocks->isNotEmpty() ? $availableStocks->first()->size_option_id : $defaultVariant->stocks->first()->size_option_id;
            }
        }

        $images = [];
        if ($product->thumbnail) {
            $images[] = Storage::url($product->thumbnail);
        } else {
            $images[] = 'https://placehold.co/600x600/f3f4f6/94a3b8?text=Produk';
        }
        foreach ($product->variants as $variant) {
            if ($variant->image) {
                $images[] = Storage::url($variant->image);
            }
        }
        $images = array_unique($images);

        $prices = $product->variants->flatMap->stocks->pluck('price')->filter();
        $minPrice = $prices->count() > 0 ? $prices->min() : 0;
        $maxPrice = $prices->count() > 0 ? $prices->max() : 0;

        $productDataArray = $product->variants->map(function ($v) {
            return [
                'id' => $v->id,
                'color' => $v->color,
                'variant_name' => $v->variant_name,
                'image_url' => $v->image ? Storage::url($v->image) : null,
                'stocks' => $v->stocks->map(function ($s) {
                    return [
                        'size_option_id' => $s->size_option_id,
                        'stock' => $s->stock,
                        'price' => $s->price
                    ];
                })->values()->all()
            ];
        })->values()->all();

        $footer = \App\Models\LandingFooter::first();
        $waNumber = $footer->phone ?? '6281907666620';
        $waNumber = preg_replace('/[^0-9]/', '', $waNumber);
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        }
        $waMessage = "Halo, saya tertarik dengan produk " . $product->product_name . ". Boleh minta info lebih lanjut?\n\nLink: " . url()->current();
        $waLink = "https://wa.me/" . $waNumber . "?text=" . urlencode($waMessage);
    @endphp

    <!-- Main Content -->
    <main class="w-full min-w-0 max-w-5xl mx-auto px-4 sm:px-6 pb-10 flex-1" x-data="productDetail()">
        <!-- Simple Toast Notification -->
        <div x-show="toast.show" x-transition x-cloak
            class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] bg-gray-900/90 backdrop-blur-sm text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-white/10">
            <template x-if="toast.type === 'success'">
                <div class="h-6 w-6 bg-emerald-500 rounded-full flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </template>
            <span class="text-sm font-bold font-outfit tracking-tight" x-text="toast.message"></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            <!-- ========== LEFT: Product Images ========== -->
            <div class="lg:col-span-5 space-y-4 min-w-0">
                <div
                    class="w-full overflow-hidden aspect-square bg-gray-50 rounded-2xl border border-gray-100 relative group">
                    <img id="mainImage" src="{{ $images[0] ?? '' }}" alt="{{ $product->product_name }}"
                        class="absolute inset-0 w-full h-full object-contain transition-transform duration-500 group-hover:scale-105">
                </div>

                @if(count($images) > 1)
                    <div class="relative group/thumb">
                        <button id="thumbPrev"
                            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 bg-white border border-gray-300 rounded-full flex items-center justify-center shadow-sm hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div id="thumbContainer" class="flex gap-2 overflow-x-auto thumb-scroll px-8">
                            @foreach($images as $i => $img)
                                <button onclick="setMainImage(this, '{{ $img }}')"
                                    class="thumb-btn shrink-0 w-14 h-14 rounded border-2 overflow-hidden {{ $i === 0 ? 'border-emerald-500' : 'border-gray-200 hover:border-emerald-300' }} transition-colors">
                                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="thumb-{{ $i }}">
                                </button>
                            @endforeach
                        </div>
                        <button id="thumbNext"
                            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 bg-white border border-gray-300 rounded-full flex items-center justify-center shadow-sm hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- ========== RIGHT: Product Info ========== -->
            <div class="lg:col-span-7 flex flex-col min-w-0">

                <!-- Product Name -->
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 leading-snug mb-5">
                    {{ $product->product_name }}
                </h1>

                <!-- Price -->
                <div class="mb-6">
                    <span id="dynamicPrice" class="text-2xl sm:text-3xl font-extrabold text-rose-500 font-outfit">
                        @if($minPrice == 0 && $maxPrice == 0)
                            Pre Order
                        @elseif($minPrice == $maxPrice)
                            Rp{{ number_format($minPrice, 0, ',', '.') }}
                        @else
                            Rp{{ number_format($minPrice, 0, ',', '.') }}
                            <span class="text-lg text-gray-300 font-light mx-1">-</span>
                            Rp{{ number_format($maxPrice, 0, ',', '.') }}
                        @endif
                    </span>
                    <div id="stockNotice" class="text-sm font-bold text-gray-500 mt-2 hidden flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="stockValue"></span>
                    </div>
                </div>

                <!-- Info Rows -->
                <div class="space-y-6 text-sm">

                    <!-- Deskripsi -->
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="text-gray-400 w-24 shrink-0 font-bold uppercase tracking-wider text-[10px]">Deskripsi
                            :</span>
                        <span
                            class="text-gray-600 leading-relaxed break-words flex-1 min-w-[200px]">{{ $product->description ?: 'Bahan kualitas premium yang cocok untuk berbagai situasi.' }}</span>
                    </div>

                    <!-- Warna -->
                    <div class="flex flex-wrap gap-2 items-start">
                        <div class="text-gray-400 w-24 shrink-0 pt-2 font-bold uppercase tracking-wider text-[10px] flex flex-col gap-1">
                            <span>Warna :</span>
                            <span class="text-[11px] font-extrabold text-emerald-600 transition-colors duration-200 block normal-case" x-text="hoveredVariantName || getSelectedVariantName()"></span>
                        </div>
                        <div class="flex flex-wrap gap-3 flex-1 max-h-[120px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-200">
                            @foreach($product->variants as $variant)
                                <button type="button"
                                    title="{{ $variant->variant_name }}"
                                    @click="selectedColor = '{{ $variant->color }}'; selectedVariant = {{ $variant->id }}; onVariantChange()"
                                    @mouseenter="hoveredVariantName = '{{ $variant->variant_name }}'"
                                    @mouseleave="hoveredVariantName = ''"
                                    :class="selectedVariant == {{ $variant->id }} ? 'ring-2 ring-emerald-500 ring-offset-2 scale-110' : 'hover:scale-110 border-gray-200'"
                                    class="w-8 h-8 rounded-full border transition-all focus:outline-none shrink-0 relative group hover:z-50"
                                    style="background-color: {{ strtolower($variant->color) }}">
                                    <!-- Tooltip showing variant name on hover -->
                                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg whitespace-nowrap z-50">
                                        {{ $variant->variant_name }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Ukuran -->
                    <div class="flex flex-wrap gap-2 items-start" x-show="selectedVariant" x-cloak>
                        <span
                            class="text-gray-400 w-24 shrink-0 pt-2.5 font-bold uppercase tracking-wider text-[10px]">Ukuran
                            :</span>
                        <div class="flex flex-wrap gap-2 flex-1">
                            @php
                                $allSizes = $product->variants->flatMap->stocks->pluck('sizeOption')->filter()->unique('id')->sortBy('order');
                            @endphp
                            @forelse($allSizes as $size)
                                <button type="button" @click="pickSize({{ $size->id }})" data-size-id="{{ $size->id }}"
                                    :class="selectedSize == {{ $size->id }} ? 'border-emerald-500 text-emerald-600 bg-emerald-50 ring-2 ring-emerald-500/10' : 'border-gray-200 text-gray-700 hover:border-emerald-300'"
                                    class="size-btn min-w-[44px] h-10 px-4 border rounded-xl text-sm font-bold transition-all focus:outline-none flex items-center justify-center break-words">
                                    {{ $size->name }}
                                </button>
                            @empty
                                <span class="text-sm text-gray-400 italic font-medium">One size</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap items-center gap-4 mt-8">
                    <a href="{{ $waLink }}" target="_blank"
                        class="flex-1 max-w-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-4 rounded-2xl transition-all shadow-xl shadow-emerald-200 active:scale-95 flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                        Hubungi Kami
                    </a>
                    <button type="button" @click="shareProduct()"
                        class="flex flex-col items-center justify-center p-3 rounded-2xl text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all active:scale-90 border border-transparent hover:border-indigo-100"
                        title="Share">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span class="text-[10px] font-bold mt-1 uppercase tracking-tighter">Share</span>
                    </button>

                </div>

            </div>
        </div>

        <!-- Stock Section -->
        <div class="mt-20 w-full">
            <h2
                class="text-xl font-black text-gray-900 mb-8 font-outfit uppercase tracking-tight flex items-center gap-3">
                <span class="w-8 h-1 bg-gray-900 rounded-full"></span>
                Stock Product
            </h2>

            @php
                $tableSizes = $product->variants->flatMap->stocks->pluck('sizeOption')->filter()->unique('id')->sortBy('order')->values();
                if ($tableSizes->where('order', 0)->count() == $tableSizes->count()) {
                    $tableSizes = $tableSizes->sortBy('id');
                }
            @endphp

            <div class="overflow-x-auto max-h-[500px] overflow-y-auto border border-gray-200 rounded-xl scrollbar-thin scrollbar-thumb-gray-200">
                <table class="w-full text-sm text-center border-collapse min-w-max">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-bold tracking-widest sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th rowspan="2"
                                class="border-b border-r border-gray-200 px-6 py-4 align-middle bg-gray-50 w-48 text-left sticky left-0 z-30">
                                Varian</th>
                            @if($tableSizes->count() > 0)
                                <th colspan="{{ $tableSizes->count() }}"
                                    class="border-b border-gray-200 px-6 py-3 bg-gray-50">Size</th>
                            @else
                                <th class="border-b border-gray-200 px-6 py-3 bg-gray-50">Size</th>
                            @endif
                        </tr>
                        <tr>
                            @forelse ($tableSizes as $size)
                                <th class="border-b border-r border-gray-200 px-4 py-3 bg-gray-50">{{ $size->name }}</th>
                            @empty
                                <th class="border-b border-gray-200 px-4 py-3 bg-gray-50">-</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($product->variants as $variant)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td
                                    class="border-r border-b border-gray-200 px-6 py-4 text-left bg-white font-bold group-hover:bg-gray-50 transition-colors sticky left-0 z-10">
                                    <div class="flex items-center gap-4">
                                        <span class="w-4 h-4 rounded-full border border-gray-100 shadow-sm"
                                            style="background-color: {{ strtolower($variant->color) }};"></span>
                                        <span class="whitespace-nowrap">{{ $variant->variant_name }}</span>
                                    </div>
                                </td>

                                @forelse ($tableSizes as $size)
                                    @php
                                        $stock = $variant->stocks->firstWhere('size_option_id', $size->id);
                                    @endphp
                                    <td
                                        class="border-r border-b border-gray-200 px-4 py-4 {{ $stock && $stock->stock <= 5 ? 'text-rose-500 font-black' : '' }}">
                                        {{ $stock ? (int) $stock->stock : 0 }}
                                    </td>
                                @empty
                                    <td class="border-r border-b border-gray-200 px-4 py-4 text-gray-300">0</td>
                                @endforelse
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Other Products Section -->
        <div class="mt-20 w-full mb-12">
            <div class="flex flex-col sm:flex-row justify-between sm:items-end gap-4 sm:gap-0 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 font-outfit tracking-tight uppercase">Produk Lainnya
                    </h2>
                    <p class="text-gray-400 text-xs mt-1 font-medium">Mungkin Anda juga menyukai ini</p>
                </div>
                <a href="/products"
                    class="bg-gray-50 hover:bg-emerald-50 text-emerald-600 px-5 py-2.5 rounded-xl text-xs font-black transition-all border border-transparent hover:border-emerald-100 flex items-center justify-center gap-2 group w-full sm:w-auto">
                    LIHAT SEMUA
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                    </svg>
                </a>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-8 snap-x thumb-scroll">
                @foreach ($otherProducts as $other)
                    @php
                        $oPriceMin = $other->variants->flatMap->stocks->min('price');
                        $oPriceMax = $other->variants->flatMap->stocks->max('price');
                    @endphp
                    <a href="/products/{{ $other->id }}"
                        class="group block min-w-[200px] max-w-[220px] flex-shrink-0 bg-white rounded-[1.5rem] shadow-sm border border-gray-100 hover:shadow-xl hover:shadow-emerald-900/5 hover:-translate-y-1 transition-all duration-300 snap-start flex flex-col overflow-hidden">
                        <div class="w-full aspect-[4/5] bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if ($other->thumbnail)
                                <img src="{{ Storage::url($other->thumbnail) }}" alt="{{ $other->product_name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <img src="https://placehold.co/400x500/f3f4f6/94a3b8?text=Produk" alt="Placeholder"
                                    class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <h3
                                class="text-gray-900 font-bold text-sm line-clamp-2 leading-tight group-hover:text-emerald-600 transition-colors mb-2 font-outfit uppercase tracking-tight">
                                {{ $other->product_name }}
                            </h3>
                            <div class="mt-auto pt-2">
                                <span class="text-rose-500 font-black text-sm tracking-tight font-outfit">
                                    @if($oPriceMin && $oPriceMax && $oPriceMin !== $oPriceMax)
                                        Rp{{ number_format($oPriceMin, 0, ',', '.') }} -
                                        Rp{{ number_format($oPriceMax, 0, ',', '.') }}
                                    @elseif($oPriceMin)
                                        Rp{{ number_format($oPriceMin, 0, ',', '.') }}
                                    @else
                                        Rp0
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    </main>

    <!-- Footer -->
    <x-layouts.footer />

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productDetail', () => ({
                selectedColor: '{{ $defaultVariant ? $defaultVariant->color : '' }}',
                selectedVariant: {{ $defaultVariant ? $defaultVariant->id : 'null' }},
                selectedSize: null,
                hoveredVariantName: '',
                productData: @json($productDataArray),
                defaultPriceRange: `@if($minPrice == 0 && $maxPrice == 0) Pre Order @elseif($minPrice == $maxPrice) Rp{{ number_format($minPrice, 0, ',', '.') }} @else Rp{{ number_format($minPrice, 0, ',', '.') }} <span class="text-lg text-gray-300 font-light mx-1">-</span> Rp{{ number_format($maxPrice, 0, ',', '.') }} @endif`,
                isLoading: false,
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                getSelectedVariantName() {
                    const variant = this.productData.find(v => v.id == this.selectedVariant);
                    return variant ? variant.variant_name : '';
                },

                init() {
                    if (this.selectedVariant) {
                        const variant = this.productData.find(v => v.id == this.selectedVariant);
                        if (variant && variant.image_url) {
                            setMainImage(null, variant.image_url);
                        }
                        
                        // Use default size from backend if set
                        @if(isset($defaultSizeId) && $defaultSizeId)
                            this.selectedSize = {{ $defaultSizeId }};
                        @else
                            if (variant && variant.stocks.length > 0) {
                                const availableStock = variant.stocks.find(s => s.stock > 0);
                                if (availableStock) this.selectedSize = availableStock.size_option_id;
                            }
                        @endif
                    }
                    this.updateUI();
                },

                get availableVariants() {
                    if (!this.selectedColor) return [];
                    return this.productData.filter(v => v.color === this.selectedColor);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number).replace('Rp', 'Rp').replace(',00', '');
                },

                pickSize(id) {
                    this.selectedSize = id;
                    this.updateUI();
                },

                onVariantChange() {
                    const variant = this.productData.find(v => v.id == this.selectedVariant);
                    if (variant && variant.image_url) {
                        setMainImage(null, variant.image_url);
                    }
                    this.selectedSize = null;
                    if (variant && variant.stocks.length > 0) {
                        const availableStock = variant.stocks.find(s => s.stock > 0);
                        if (availableStock) {
                            this.selectedSize = availableStock.size_option_id;
                        }
                    }
                    this.updateUI();
                },

                updateUI() {
                    const priceEl = document.getElementById('dynamicPrice');
                    const noticeEl = document.getElementById('stockNotice');
                    const stockValueEl = document.getElementById('stockValue');
                    if (!priceEl || !noticeEl) return;

                    if (this.selectedVariant && this.selectedSize) {
                        const variant = this.productData.find(v => v.id == this.selectedVariant);
                        const stockItem = variant ? variant.stocks.find(s => s.size_option_id == this.selectedSize) : null;

                        if (stockItem && stockItem.stock > 0) {
                            if (stockItem.price == 0) {
                                priceEl.innerHTML = 'Pre Order';
                            } else {
                                priceEl.innerHTML = this.formatRupiah(stockItem.price);
                            }
                            noticeEl.classList.remove('hidden');
                            if (stockValueEl) stockValueEl.innerText = 'Sisa Stok: ' + parseInt(stockItem.stock);
                        } else {
                            priceEl.innerHTML = this.defaultPriceRange;
                            noticeEl.classList.remove('hidden');
                            if (stockValueEl) stockValueEl.innerText = 'Stok Kosong';
                        }
                    } else {
                        priceEl.innerHTML = this.defaultPriceRange;
                        noticeEl.classList.add('hidden');
                    }

                    document.querySelectorAll('.size-btn').forEach(btn => {
                        const sId = parseInt(btn.getAttribute('data-size-id'));
                        let hasStock = false;
                        if (this.selectedVariant) {
                            const v = this.productData.find(v => v.id == this.selectedVariant);
                            if (v) {
                                const s = v.stocks.find(s => s.size_option_id == sId);
                                if (s && s.stock > 0) hasStock = true;
                            }
                        }

                        if (!hasStock) {
                            btn.classList.add('opacity-40', 'cursor-not-allowed', 'bg-gray-50');
                        } else {
                            btn.classList.remove('opacity-40', 'cursor-not-allowed', 'bg-gray-50');
                        }
                    });
                },

                isValidSelection() {
                    if (!this.selectedVariant || !this.selectedSize) return false;
                    const v = this.productData.find(v => v.id == this.selectedVariant);
                    const s = v ? v.stocks.find(s => s.size_option_id == this.selectedSize) : null;
                    return s && s.stock > 0;
                },

                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                },

                async shareProduct() {
                    const shareData = {
                        title: '{{ $product->product_name }} - KonveksiHub',
                        text: 'Cek produk keren ini di KonveksiHub!',
                        url: window.location.href
                    };

                    if (navigator.share) {
                        try {
                            await navigator.share(shareData);
                        } catch (err) {
                            if (err.name !== 'AbortError') {
                                this.copyToClipboard();
                            }
                        }
                    } else {
                        this.copyToClipboard();
                    }
                },

                copyToClipboard() {
                    try {
                        navigator.clipboard.writeText(window.location.href);
                        this.showToast('Link berhasil disalin!');
                    } catch (e) {
                        const el = document.createElement('textarea');
                        el.value = window.location.href;
                        document.body.appendChild(el);
                        el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                        this.showToast('Link berhasil disalin!');
                    }
                },

                async addToCart() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: {{ $product->id }},
                                variant_id: this.selectedVariant,
                                size_option_id: this.selectedSize,
                                quantity: 1
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.showToast('Produk ditambahkan ke keranjang!');
                            if (window.updateNavCartCount) window.updateNavCartCount(result.cartCount);
                        } else {
                            alert(result.message || 'Gagal menambahkan ke keranjang');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });

        function setMainImage(btn, src) {
            var img = document.getElementById('mainImage');
            if (!img) return;
            img.style.opacity = '0';
            setTimeout(function () {
                img.src = src;
                img.style.opacity = '1';
            }, 300);

            if (btn) {
                document.querySelectorAll('.thumb-btn').forEach(function (b) {
                    b.classList.remove('border-emerald-500');
                    b.classList.add('border-gray-200');
                });
                btn.classList.remove('border-gray-200');
                btn.classList.add('border-emerald-500');
            }
        }

        (function () {
            var c = document.getElementById('thumbContainer');
            var prev = document.getElementById('thumbPrev');
            var next = document.getElementById('thumbNext');
            if (!c || !prev || !next) return;
            prev.addEventListener('click', function () { c.scrollBy({ left: -200, behavior: 'smooth' }); });
            next.addEventListener('click', function () { c.scrollBy({ left: 200, behavior: 'smooth' }); });
        })();
    </script>
</body>

</html>