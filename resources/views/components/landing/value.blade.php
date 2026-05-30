<!-- Our Values / Layanan -->
<section id="layanan" class="bg-white relative py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-4 block">Our Values</span>
            <h2 class="font-outfit text-3xl md:text-5xl font-black text-slate-900 mb-6">Kenapa Memilih Kami?</h2>
            <div class="w-24 h-2 bg-indigo-600 mx-auto rounded-full"></div>
        </div>

        <div class="flex flex-wrap justify-center gap-8">
            @php
                $values = \App\Models\LandingValue::orderBy('sort_order')->limit(5)->get();
                $count = $values->count() ?: 3;
                $cardWidth = "calc((100% - (2rem * (" . ($count - 1) . "))) / " . $count . ")";
            @endphp

            @if($values->count() > 0)
                @foreach($values as $value)
                    <div
                        class="group p-6 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 text-center w-full sm:w-[calc(50%-1rem)] lg:w-[var(--card-width)]"
                        style="--card-width: {{ $cardWidth }};">
                        <div
                            class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110 overflow-hidden border border-indigo-100 mx-auto">
                            <img src="{{ asset('storage/' . $value->image) }}" class="w-full h-full object-cover"
                                alt="{{ $value->title }}">
                        </div>
                        <h3 class="font-outfit text-xl font-bold mb-3">{{ $value->title }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ $value->description }}</p>
                    </div>
                @endforeach
            @else
                @php
                    $fallbackWidth = "calc((100% - (2rem * 2)) / 3)";
                @endphp
                <!-- Fallback Value Card 1 -->
                <div
                    class="group p-6 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 text-center w-full sm:w-[calc(50%-1rem)] lg:w-[var(--card-width)]"
                    style="--card-width: {{ $fallbackWidth }};">
                    <div
                        class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110 mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-outfit text-xl font-bold mb-3">DESAIN CUSTOM</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Bebas buat desain sendiri dengan bantuan tim ahli kami. Kami
                        memandu Anda dari sketsa hingga sampel fisik produk.</p>
                </div>

                <!-- Fallback Value Card 2 -->
                <div
                    class="group p-6 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 text-center w-full sm:w-[calc(50%-1rem)] lg:w-[var(--card-width)]"
                    style="--card-width: {{ $fallbackWidth }};">
                    <div
                        class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 transition-transform duration-500 group-hover:-rotate-12 group-hover:scale-110 mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-outfit text-xl font-bold mb-3">KUALITAS TERJAMIN</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Bahan pilihan kualitas ekspor dengan teknik pengerjaan
                        presisi. Quality Control berlapis di setiap tahap produksi.</p>
                </div>

                <!-- Fallback Value Card 3 -->
                <div
                    class="group p-6 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 text-center w-full sm:w-[calc(50%-1rem)] lg:w-[var(--card-width)]"
                    style="--card-width: {{ $fallbackWidth }};">
                    <div
                        class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110 mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-outfit text-xl font-bold mb-3">TEPAT WAKTU</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Jaminan ketepatan jadwal produksi dan rute distribusi.
                        Kecepatan tanpa mengorbankan kualitas adalah prinsip kami.</p>
                </div>
            @endif
        </div>
    </div>
</section>