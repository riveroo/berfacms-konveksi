<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->product_name }} - KonveksiHub</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 pt-24">
        <nav class="text-xs text-gray-400 flex items-center gap-1">
            <a href="/" class="hover:text-indigo-600">Home</a>
            <span>›</span>
            <a href="{{ route('products.index') }}" class="hover:text-indigo-600">Katalog</a>
            <span>›</span>
            <span class="text-gray-600 truncate max-w-[180px]">{{ $product->product_name }}</span>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 pb-10 flex-1">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            @php
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
            @endphp

            <!-- ========== LEFT: Image Gallery ========== -->
            <div class="lg:col-span-5">
                <!-- Main Image -->
                <div class="w-full bg-gray-50 rounded-lg overflow-hidden border border-gray-200 flex items-center justify-center group cursor-crosshair"
                    style="max-height:380px;">
                    <img id="mainImage" src="{{ $images[0] }}" alt="{{ $product->product_name }}"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-300" style="max-height:380px;">
                </div>

                <!-- Thumbnail Strip -->
                @if(count($images) > 1)
                    <div class="relative mt-3">
                        <button id="thumbPrev"
                            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 bg-white border border-gray-300 rounded-full flex items-center justify-center shadow-sm hover:bg-gray-50 -ml-1">
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
                            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-7 h-7 bg-white border border-gray-300 rounded-full flex items-center justify-center shadow-sm hover:bg-gray-50 -mr-1">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- ========== RIGHT: Product Info ========== -->
            <div class="lg:col-span-7 flex flex-col">

                <!-- Product Name -->
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 leading-snug mb-5">
                    {{ $product->product_name }}
                </h1>

                <!-- Price -->
                @php
                    $prices = $product->variants->flatMap->stocks->pluck('price')->filter();
                    $minPrice = $prices->count() > 0 ? $prices->min() : 0;
                    $maxPrice = $prices->count() > 0 ? $prices->max() : 0;
                @endphp
                <div class="mb-6">
                    <span id="dynamicPrice" class="text-2xl sm:text-3xl font-extrabold text-rose-500">
                        @if($minPrice == $maxPrice)
                            Rp{{ number_format($minPrice, 0, ',', '.') }}
                        @else
                            Rp{{ number_format($minPrice, 0, ',', '.') }}
                            <span class="text-lg text-gray-300 font-light mx-1">-</span>
                            Rp{{ number_format($maxPrice, 0, ',', '.') }}
                        @endif
                    </span>
                    <div id="stockNotice" class="text-sm font-medium text-gray-500 mt-2 hidden"></div>
                </div>

                <!-- Info Rows (table-like label : value layout) -->
                <div class="space-y-5 text-sm">

                    <!-- Deskripsi -->
                    <div class="flex gap-4">
                        <span class="text-gray-400 w-24 shrink-0">Deskripsi :</span>
                        <span
                            class="text-gray-700 leading-relaxed">{{ $product->description ?: 'Bahan kualitas premium yang cocok untuk berbagai situasi.' }}</span>
                    </div>

                    <!-- Ukuran -->
                    <div class="flex gap-4 items-start">
                        <span class="text-gray-400 w-24 shrink-0 pt-1.5">Ukuran :</span>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $allSizes = $product->variants->flatMap->stocks->pluck('sizeOption')->filter()->unique('id')->sortBy('id');
                            @endphp
                            @forelse($allSizes as $size)
                                <button onclick="pickSize(this, {{ $size->id }})" data-size-id="{{ $size->id }}"
                                    class="size-btn min-w-[44px] h-9 px-3 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:border-emerald-500 hover:text-emerald-600 transition-colors focus:outline-none">
                                    {{ $size->name }}
                                </button>
                            @empty
                                <span class="text-sm text-gray-400 italic">One size</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Warna -->
                    <div class="flex gap-4 items-start">
                        <span class="text-gray-400 w-24 shrink-0 pt-1.5">Warna :</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <button onclick="pickColor(this, {{ $variant->id }})" data-variant-id="{{ $variant->id }}" data-image="{{ $variant->image ? Storage::url($variant->image) : '' }}"
                                    class="color-btn px-4 h-9 border border-gray-300 rounded text-sm font-semibold text-gray-700 hover:border-emerald-500 hover:text-emerald-600 transition-colors focus:outline-none flex gap-2 items-center">
                                    <span class="w-3 h-3 rounded-full border border-gray-400" style="background-color: {{ strtolower($variant->color) }}"></span>
                                    {{ $variant->variant_name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kuantitas -->
                    <div class="flex gap-4 items-center">
                        <span class="text-gray-400 w-24 shrink-0">Kuantitas :</span>
                        <div class="flex items-center border border-gray-300 rounded h-9 overflow-hidden">
                            <button onclick="adjustQty(-1)"
                                class="w-9 h-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </button>
                            <input id="qtyInput" type="text" value="1"
                                class="w-10 h-full text-center text-sm font-semibold text-gray-800 border-x border-gray-300 focus:outline-none bg-white"
                                readonly>
                            <button onclick="adjustQty(1)"
                                class="w-9 h-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex items-center gap-4 mt-8">
                    <button type="button" 
                        onclick="addToCart()"
                        id="addToCartBtn"
                        class="flex-1 max-w-xs bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 rounded-lg transition-colors focus:outline-none shadow-sm disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Tambah ke troli
                    </button>
                    <button type="button"
                        class="flex items-center gap-1.5 text-gray-400 hover:text-gray-600 transition-colors"
                        title="Share">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span class="text-xs font-medium">Share</span>
                    </button>
                    <button type="button"
                        class="flex items-center gap-1.5 text-gray-400 hover:text-rose-500 transition-colors"
                        title="Like">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                        <span class="text-xs font-medium">Like</span>
                    </button>
                </div>

            </div>
        </div>
        <span></span>
        <!-- Stock Section -->
        <div class="mt-14 w-full">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Stock Product</h2>

            @php
                $tableSizes = $product->variants->flatMap->stocks->pluck('sizeOption')->filter()->unique('id')->sortBy('order')->values();
                if ($tableSizes->where('order', 0)->count() == $tableSizes->count()) {
                    $tableSizes = $tableSizes->sortBy('id'); // Fallback if all order are 0
                }
            @endphp

            <div class="overflow-x-auto rounded-lg border border-gray-300">
                <table class="w-full text-sm text-center border-collapse">
                    <thead class="bg-gray-100 text-gray-800 font-semibold">
                        <tr>
                            <th rowspan="2" class="border border-gray-300 px-4 py-3 align-middle bg-white w-48">Varian
                            </th>
                            @if($tableSizes->count() > 0)
                                <th colspan="{{ $tableSizes->count() }}" class="border border-gray-300 px-4 py-2 bg-white">
                                    Size</th>
                            @else
                                <th class="border border-gray-300 px-4 py-2 bg-white">Size</th>
                            @endif
                        </tr>
                        <tr>
                            @forelse ($tableSizes as $size)
                                <th class="border border-gray-300 px-4 py-2 bg-white">{{ $size->name }}</th>
                            @empty
                                <th class="border border-gray-300 px-4 py-2 bg-white">-</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 bg-white">
                        @foreach ($product->variants as $variant)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="border border-gray-300 px-4 py-3 text-left">
                                    <div class="flex items-center gap-3">
                                        <span class="w-5 h-5 border border-gray-800 shadow-sm"
                                            style="background-color: {{ strtolower($variant->color) }};"></span>
                                        <span class="whitespace-nowrap">{{ $variant->variant_name }}</span>
                                    </div>
                                </td>

                                @forelse ($tableSizes as $size)
                                    @php
                                        $stock = $variant->stocks->firstWhere('size_option_id', $size->id);
                                    @endphp
                                    <td class="border border-gray-300 px-4 py-3">
                                        {{ $stock ? $stock->stock : 0 }}
                                    </td>
                                @empty
                                    <td class="border border-gray-300 px-4 py-3 text-gray-400">0</td>
                                @endforelse
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Other Products Section -->
        <div class="mt-16 w-full mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900 border-l-4 border-emerald-500 pl-3">
                    {{ __('products.other_products') }}
                </h2>
                <a href="/products"
                    class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold transition-colors flex items-center gap-1">
                    {{ __('products.view_all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-6 snap-x scrollbar-hide">
                @foreach ($otherProducts as $other)
                    @php
                        $oPriceMin = $other->variants->flatMap->stocks->min('price');
                        $oPriceMax = $other->variants->flatMap->stocks->max('price');
                    @endphp
                    <a href="/products/{{ $other->id }}"
                        class="group block w-[160px] md:w-[200px] shrink-0 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-emerald-200 transition-all duration-300 snap-start flex flex-col overflow-hidden">
                        <div class="w-full aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if ($other->thumbnail)
                                <img src="{{ Storage::url($other->thumbnail) }}" alt="{{ $other->product_name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <img src="https://placehold.co/400x400/f3f4f6/94a3b8?text=Produk" alt="Placeholder"
                                    class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <h3
                                class="text-gray-800 font-medium text-sm md:text-base line-clamp-2 leading-snug group-hover:text-emerald-600 transition-colors mb-2">
                                {{ $other->product_name }}
                            </h3>
                            <div class="mt-auto">
                                <span class="text-gray-900 font-bold text-sm md:text-base tracking-tight">
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
    <footer class="border-t border-gray-100 py-6 text-center text-gray-400 text-xs mt-auto">
        &copy; 2026 KonveksiHub. Hak Cipta Dilindungi.
    </footer>

    @php
        $productDataArray = $product->variants->map(function($v) {
            return [
                'id' => $v->id,
                'color' => $v->color,
                'image_url' => $v->image ? Storage::url($v->image) : null,
                'stocks' => $v->stocks->map(function($s) {
                    return [
                        'size_option_id' => $s->size_option_id,
                        'stock' => $s->stock,
                        'price' => $s->price
                    ];
                })->values()->all()
            ];
        })->values()->all();
    @endphp
    <script>
        const productData = @json($productDataArray);

        let selectedVariantId = null;
        let selectedSizeId = null;

        const defaultPriceRange = `@if($minPrice == $maxPrice) Rp{{ number_format($minPrice, 0, ',', '.') }} @else Rp{{ number_format($minPrice, 0, ',', '.') }} <span class="text-lg text-gray-300 font-light mx-1">-</span> Rp{{ number_format($maxPrice, 0, ',', '.') }} @endif`;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number).replace('Rp', 'Rp').replace(',00', '');
        }

        function updateUI() {
            const priceEl = document.getElementById('dynamicPrice');
            const noticeEl = document.getElementById('stockNotice');
            
            if (selectedVariantId && selectedSizeId) {
                const variant = productData.find(v => v.id === selectedVariantId);
                const stockItem = variant ? variant.stocks.find(s => s.size_option_id === selectedSizeId) : null;
                
                if (stockItem && stockItem.stock > 0) {
                    priceEl.innerHTML = formatRupiah(stockItem.price);
                    noticeEl.classList.remove('hidden');
                    noticeEl.innerText = 'Sisa Stok: ' + stockItem.stock;
                } else {
                    priceEl.innerHTML = defaultPriceRange;
                    noticeEl.classList.remove('hidden');
                    noticeEl.innerText = 'Stok Kosong';
                }
            } else {
                priceEl.innerHTML = defaultPriceRange;
                noticeEl.classList.add('hidden');
            }

            document.querySelectorAll('.size-btn').forEach(btn => {
                const sId = parseInt(btn.getAttribute('data-size-id'));
                let hasStock = false;
                
                if (selectedVariantId) {
                    const variant = productData.find(v => v.id === selectedVariantId);
                    if (variant) {
                        const sItem = variant.stocks.find(s => s.size_option_id === sId);
                        if (sItem && sItem.stock > 0) hasStock = true;
                    }
                } else {
                    hasStock = productData.some(v => v.stocks.some(s => s.size_option_id === sId && s.stock > 0));
                }

                if (!hasStock) {
                    btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
                } else {
                    btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
                }
            });

            document.querySelectorAll('.color-btn').forEach(btn => {
                const vId = parseInt(btn.getAttribute('data-variant-id'));
                let hasStock = false;

                if (selectedSizeId) {
                    const variant = productData.find(v => v.id === vId);
                    if (variant) {
                        const sItem = variant.stocks.find(s => s.size_option_id === selectedSizeId);
                        if (sItem && sItem.stock > 0) hasStock = true;
                    }
                } else {
                    const variant = productData.find(v => v.id === vId);
                    if (variant && variant.stocks.some(s => s.stock > 0)) hasStock = true;
                }

                if (!hasStock) {
                    btn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
                } else {
                    btn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
                }
            });
        }

        function setMainImage(btn, src) {
            var img = document.getElementById('mainImage');
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
            prev.addEventListener('click', function () { c.scrollBy({ left: -120, behavior: 'smooth' }); });
            next.addEventListener('click', function () { c.scrollBy({ left: 120, behavior: 'smooth' }); });
        })();

        function pickSize(btn, sizeId) {
            if (btn.classList.contains('cursor-not-allowed')) return;
            
            if (selectedSizeId === sizeId) {
                selectedSizeId = null;
                btn.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                btn.classList.add('border-gray-300', 'text-gray-700');
            } else {
                document.querySelectorAll('.size-btn').forEach(function (b) {
                    b.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                    b.classList.add('border-gray-300', 'text-gray-700');
                });
                selectedSizeId = sizeId;
                btn.classList.remove('border-gray-300', 'text-gray-700');
                btn.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
            }
            updateUI();
        }

        function pickColor(btn, variantId) {
            if (btn.classList.contains('cursor-not-allowed')) return;

            if (selectedVariantId === variantId) {
                selectedVariantId = null;
                btn.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                btn.classList.add('border-gray-300', 'text-gray-700');
            } else {
                document.querySelectorAll('.color-btn').forEach(function (b) {
                    b.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                    b.classList.add('border-gray-300', 'text-gray-700');
                });
                selectedVariantId = variantId;
                btn.classList.remove('border-gray-300', 'text-gray-700');
                btn.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50');
                
                const imgUrl = btn.getAttribute('data-image');
                if(imgUrl) {
                    setMainImage(null, imgUrl);
                }
            }
            updateUI();
        }

        function adjustQty(delta) {
            var input = document.getElementById('qtyInput');
            var val = parseInt(input.value) || 1;
            val += delta;
            
            // Check stock limit
            let maxStock = 9999;
            if (selectedVariantId && selectedSizeId) {
                const variant = productData.find(v => v.id === selectedVariantId);
                const stockItem = variant ? variant.stocks.find(s => s.size_option_id === selectedSizeId) : null;
                if (stockItem) maxStock = stockItem.stock;
            }

            if (val > maxStock) val = maxStock;
            if (val < 1) val = 1;

            input.value = val;
        }

        function addToCart() {
            if (!selectedVariantId || !selectedSizeId) {
                alert('Silakan pilih warna dan ukuran terlebih dahulu');
                return;
            }

            const qty = document.getElementById('qtyInput').value;
            const btn = document.getElementById('addToCartBtn');
            const originalText = btn.innerText;

            btn.disabled = true;
            btn.innerText = 'Menambahkan...';

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    variant_id: selectedVariantId,
                    size_option_id: selectedSizeId,
                    quantity: qty
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    alert('Produk berhasil ditambahkan ke keranjang!');
                    window.location.reload(); // Reload to update navbar count
                } else {
                    alert(res.body.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menambahkan ke keranjang');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = originalText;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mainImage').style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            updateUI();
        });
    </script>
</body>

</html>