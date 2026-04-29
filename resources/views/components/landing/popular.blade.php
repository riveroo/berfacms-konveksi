<!-- Popular Products Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="font-outfit text-3xl md:text-5xl font-black text-slate-900">Produk Terpopuler</h2>
                <div class="w-16 h-1 bg-indigo-600 mt-4 rounded-full"></div>
            </div>
            <a href="{{ route('products.index') }}"
                class="hidden md:inline-flex text-indigo-600 font-bold hover:text-indigo-700 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        @php
            $populars = \App\Models\LandingPopularProduct::with('product')->orderBy('sort_order')->limit(4)->get();
        @endphp

        @if($populars->count() > 0)
            <div class="flex gap-6 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-8 overflow-x-auto md:overflow-visible pb-8 md:pb-0 snap-x snap-mandatory scrollbar-hide -mx-6 px-6 md:mx-0 md:px-0">
                @foreach($populars as $popular)
                    @php $product = $popular->product; @endphp
                    @if($product)
                    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="snap-center shrink-0 w-[280px] md:w-auto block">
                        <div
                            class="group bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                            <div class="relative aspect-square overflow-hidden bg-white p-4">
                                @if($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                    class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105"
                                    alt="{{ $product->product_name }}">
                                @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                @endif
                                <div
                                    class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                    Populer
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="font-outfit font-bold text-lg text-slate-900 mb-2 line-clamp-1">{{ $product->product_name }}</h3>
                                <!-- Optional Price if it exists in your product model -->
                                @if(isset($product->price) || isset($product->stocks))
                                    <p class="text-indigo-600 font-black text-xl">Rp {{ number_format($product->price ?? ($product->stocks->first()->price ?? 0), 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex gap-6 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-8 overflow-x-auto md:overflow-visible pb-8 md:pb-0 snap-x snap-mandatory scrollbar-hide -mx-6 px-6 md:mx-0 md:px-0">
                @for ($i = 1; $i <= 4; $i++)
                    <div
                        class="snap-center shrink-0 w-[280px] md:w-auto group bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 cursor-pointer">
                        <div class="relative aspect-square overflow-hidden bg-white p-4">
                            <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=800&auto=format&fit=crop"
                                class="w-full h-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105"
                                alt="Popular Product">
                            <div
                                class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                Populer
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="font-outfit font-bold text-lg text-slate-900 mb-2 line-clamp-1">T-Shirt Premium Custom
                                {{ $i }}</h3>
                            <p class="text-indigo-600 font-black text-xl">Rp 85.000</p>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
        <div class="mt-8 text-center md:hidden">
            <x-button variant="outline" class="w-full" href="{{ route('products.index') }}">Lihat Semua
                Produk</x-button>
        </div>
    </div>
</section>