<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Stok Produk | Konveksi hub</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Styles -->
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

        .stock-table th {
            @apply px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100;
        }

        .stock-table td {
            @apply px-4 py-3 text-sm border-b border-slate-50;
        }

        .low-stock {
            @apply text-rose-600 font-bold animate-pulse;
        }

        .out-of-stock {
            @apply text-slate-300 font-medium;
        }
    </style>
</head>

<body class="antialiased bg-slate-50 text-slate-900">

    <x-layouts.header />

    <main class="pt-12 pb-24">
        <div class="container mx-auto px-6">
            <!-- Page Header -->
            <div class="max-w-4xl mb-1">
                <span class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-4 block">
                    Stok Online</span>
                <h1 class="font-outfit text-4xl md:text-5xl font-black text-slate-900 mb-4">CEK STOK PRODUK</h1>
                <p class="text-lg text-slate-600">Lihat ketersediaan produk kami secara real-time. Stok diperbarui
                    secara otomatis setiap ada transaksi baru.</p>
            </div>

            <!-- Search & Filter -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-200 mb-12">
                <form action="{{ route('public.stock') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                    <div class="relative flex-1 group">
                        <div
                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama produk..."
                            class="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                    </div>

                    <div class="relative min-w-[240px]">
                        <select name="type_id"
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all font-medium appearance-none">
                            <option value="">Semua Kategori</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type->id }}" {{ $typeId == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-400">

                        </div>
                    </div>

                    <x-button type="submit" variant="indigo"
                        class="px-10 rounded-2xl h-14 shadow-lg shadow-indigo-600/20">
                        CARI DATA
                    </x-button>
                </form>
            </div>

            <!-- Stock List -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden mb-12">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead
                            class="bg-slate-50 uppercase text-[10px] text-slate-500 font-bold tracking-wider sticky top-0 z-10 shadow-sm shadow-slate-200/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-slate-50">ID</th>
                                <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-slate-50">Product Name
                                </th>
                                <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-slate-50">Variant Name
                                </th>
                                <th scope="col" class="px-6 py-4 text-left whitespace-nowrap bg-slate-50">Color</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left whitespace-nowrap bg-slate-50 border-r border-slate-200">
                                    Type</th>
                                @foreach($sizes as $size)
                                    <th scope="col"
                                        class="px-4 py-4 text-center whitespace-nowrap bg-slate-50 border-l border-slate-100">
                                        {{ $size->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($variants as $variant)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-900">
                                        {{ $variant->variant_code ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                        <a href="{{ route('products.show', $variant->product_id) }}"
                                            class="hover:text-indigo-600 transition-colors">
                                            {{ optional($variant->product)->product_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-medium">
                                        {{ $variant->variant_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                        <div class="flex items-center justify-center">
                                            @if($variant->color)
                                                <span class="w-5 h-5 rounded-full border border-slate-300 shadow-sm"
                                                    style="background-color: {{ $variant->color }}"></span>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 border-r border-slate-200">
                                        {{ optional($variant->productType)->name ?: '-' }}
                                    </td>
                                    @foreach($sizes as $size)
                                        @php
                                            $stockItem = $variant->stocks->firstWhere('size_option_id', $size->id);
                                            $qty = $stockItem ? $stockItem->stock : 0;
                                        @endphp
                                        <td class="px-4 py-4 whitespace-nowrap text-center border-l border-slate-50">
                                            @if($qty == 0)
                                                <span class="text-slate-300 font-medium">0</span>
                                            @elseif($qty <= 5)
                                                <span class="text-rose-600 font-black animate-pulse">{{ $qty }}</span>
                                            @else
                                                <span class="font-bold text-slate-900">{{ $qty }}</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 5 + count($sizes) }}" class="px-6 py-24 text-center">
                                        <div
                                            class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <h2 class="font-outfit text-xl font-black text-slate-900 mb-1">DATA TIDAK DITEMUKAN
                                        </h2>
                                        <p class="text-slate-500">Maaf, kami tidak menemukan data stok untuk pencarian Anda.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $variants->links() }}
            </div>

            <!-- Bottom CTA -->
            <div class="mt-24 p-12 bg-indigo-600 rounded-[3rem] text-center relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-indigo-900 opacity-20 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent">
                </div>
                <div class="relative z-10">
                    <h2 class="font-outfit text-3xl md:text-4xl font-black text-white mb-6 uppercase">SUDAH MENEMUKAN
                        YANG ANDA CARI?</h2>
                    <p class="text-indigo-100 text-lg mb-10 max-w-xl mx-auto">Klik tombol di bawah ini untuk memulai
                        pemesanan via WhatsApp atau melihat detail produk lebih lanjut.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">

                        <a href="https://wa.me/6281907666620" target="_blank"
                            class="inline-flex items-center justify-center gap-3 px-12 h-16 bg-[#25D366] hover:bg-[#1ebd5a] text-white font-bold rounded-2xl text-lg shadow-xl shadow-green-500/20 transition-all transform hover:-translate-y-1">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.348-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            HUBUNGI VIA WA
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