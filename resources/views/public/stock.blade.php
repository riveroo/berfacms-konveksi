<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Stok Produk | Konveksi hub</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        
        .stock-table th { @apply px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100; }
        .stock-table td { @apply px-4 py-3 text-sm border-b border-slate-50; }
        
        .low-stock { @apply text-rose-600 font-bold animate-pulse; }
        .out-of-stock { @apply text-slate-300 font-medium; }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900">

    <x-layouts.header />

    <main class="pt-32 pb-24">
        <div class="container mx-auto px-6">
            <!-- Page Header -->
            <div class="max-w-4xl mb-12">
                <span class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-4 block">Inventory Check</span>
                <h1 class="font-outfit text-4xl md:text-5xl font-black text-slate-900 mb-4">CEK STOK PRODUK</h1>
                <p class="text-lg text-slate-600">Lihat ketersediaan produk kami secara real-time. Stok diperbarui secara otomatis setiap ada transaksi baru.</p>
            </div>

            <!-- Search & Filter -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 mb-12">
                <form action="{{ route('public.stock') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama produk..." class="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                    </div>
                    
                    <div class="relative min-w-[240px]">
                        <select name="type_id" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium appearance-none">
                            <option value="">Semua Kategori</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type->id }}" {{ $typeId == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <x-button type="submit" variant="indigo" class="px-10 rounded-2xl h-14 shadow-lg shadow-indigo-600/20">
                        CARI DATA
                    </x-button>
                </form>
            </div>

            <!-- Stock List -->
            <div class="grid grid-cols-1 gap-8">
                @forelse($products as $product)
                    <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-slate-200 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500 group">
                        <div class="flex flex-col xl:flex-row">
                            <!-- Left: Product Info -->
                            <div class="xl:w-1/3 p-8 bg-slate-50/50 border-b xl:border-b-0 xl:border-r border-slate-100 flex gap-6 items-center">
                                @if($product->thumbnail)
                                    <img src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->product_name }}" class="w-32 h-32 rounded-3xl object-cover shadow-md group-hover:scale-105 transition-transform duration-500 bg-white">
                                @else
                                    <div class="w-32 h-32 bg-slate-200 rounded-3xl flex items-center justify-center text-slate-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-[10px] font-bold tracking-widest uppercase mb-3 inline-block">
                                        {{ $product->variants->first()->productType->name ?? 'Uncategorized' }}
                                    </div>
                                    <h3 class="font-outfit text-2xl font-black text-slate-900 mb-2">{{ $product->product_name }}</h3>
                                    <a href="{{ route('products.show', $product->id) }}" class="text-indigo-600 text-sm font-bold flex items-center gap-2 hover:text-indigo-700 transition-colors">
                                        LIHAT PRODUK
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Right: Stock Table -->
                            <div class="xl:w-2/3 p-8 overflow-x-auto">
                                <table class="w-full stock-table text-left">
                                    <thead>
                                        <tr>
                                            <th class="min-w-[150px]">VARIAN WARNA</th>
                                            @foreach($sizes as $size)
                                                <th class="text-center">{{ $size->name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr class="hover:bg-slate-50 transition-colors duration-300">
                                                <td class="flex items-center gap-3">
                                                    <div class="w-4 h-4 rounded-full border border-slate-200" style="background-color: {{ strtolower($variant->color ?: '#ccc') }}"></div>
                                                    <span class="font-bold text-slate-700">{{ $variant->variant_name }}</span>
                                                </td>
                                                @foreach($sizes as $size)
                                                    @php
                                                        $stock = $variant->stocks->where('size_option_id', $size->id)->first();
                                                        $qty = $stock ? $stock->stock : 0;
                                                        $min = 5; // Static threshold for low stock indicator
                                                    @endphp
                                                    <td class="text-center">
                                                        @if($qty == 0)
                                                            <span class="out-of-stock">HABIS</span>
                                                        @elseif($qty <= $min)
                                                            <span class="low-stock">{{ $qty }}</span>
                                                        @else
                                                            <span class="font-black text-slate-900">{{ $qty }}</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-24 rounded-[3rem] text-center border border-slate-200">
                        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h2 class="font-outfit text-2xl font-black text-slate-900 mb-2">PRODUK TIDAK DITEMUKAN</h2>
                        <p class="text-slate-500">Maaf, kami tidak menemukan data stok untuk pencarian Anda.</p>
                        <x-button href="{{ route('public.stock') }}" variant="outline" class="mt-8 rounded-2xl px-8 border-slate-200">LIHAT SEMUA</x-button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $products->links() }}
            </div>

            <!-- Bottom CTA -->
            <div class="mt-24 p-12 bg-indigo-600 rounded-[3rem] text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-indigo-900 opacity-20 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent"></div>
                <div class="relative z-10">
                    <h2 class="font-outfit text-3xl md:text-4xl font-black text-white mb-6 uppercase">SUDAH MENEMUKAN YANG ANDA CARI?</h2>
                    <p class="text-indigo-100 text-lg mb-10 max-w-xl mx-auto">Klik tombol di bawah ini untuk memulai pemesanan via WhatsApp atau melihat detail produk lebih lanjut.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <x-button href="{{ route('products.index') }}" variant="white" class="px-12 rounded-2xl h-16 text-indigo-600 text-lg shadow-xl shadow-black/10">PESAN SEKARANG</x-button>
                        <a href="https://wa.me/62821XXXXXXXX" target="_blank" class="inline-flex items-center justify-center gap-3 px-12 h-16 bg-[#25D366] hover:bg-[#1ebd5a] text-white font-bold rounded-2xl text-lg shadow-xl shadow-green-500/20 transition-all transform hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.348-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            KONSULTASI VIA WA
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer id="kontak" class="bg-slate-950 pt-24 pb-12 text-slate-400">
        <div class="container mx-auto px-6">
            <div class="pt-8 border-t border-slate-900 text-center">
                <p>Copyright &copy; {{ date('Y') }} Konveksi hub. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
