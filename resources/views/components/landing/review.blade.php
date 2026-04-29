@php
    $reviews = \App\Models\LandingReview::orderBy('sort_order')->get();
@endphp

<!-- Client Reviews Section -->
<section id="tentang" class="bg-slate-50 py-16 md:py-24 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        <div class="mb-12 md:mb-16">
            <span class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-4 block">Testimonial</span>
            <h2 class="font-outfit text-3xl md:text-5xl font-black text-slate-900 mb-4">Apa Kata Klien Kami</h2>
            <div class="w-16 h-1 bg-indigo-600 rounded-full"></div>
        </div>

        @if($reviews->count() > 0)
            <!-- Horizontal Scroll Container -->
            <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory scrollbar-hide -mx-6 px-6 md:mx-0 md:px-0">
                @foreach($reviews as $review)
                    <div class="snap-center shrink-0 w-[300px] md:w-[380px] bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative group transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <!-- Quote Icon -->
                        <svg class="w-10 h-10 text-indigo-50 absolute top-8 right-8 z-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>

                        <div class="relative z-10 flex flex-col h-full">
                            <p class="text-base text-slate-700 leading-relaxed italic mb-8 flex-grow">
                                "{{ $review->review_text }}"
                            </p>
                            
                            <div class="flex items-center gap-4 mt-auto">
                                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center font-bold text-white text-lg shadow-lg shadow-indigo-600/30 shrink-0 uppercase">
                                    {{ substr($review->reviewer_name, 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="font-outfit text-base font-black text-slate-900 line-clamp-1">{{ $review->reviewer_name }}</h5>
                                    <p class="text-slate-500 text-xs font-medium line-clamp-1">{{ $review->client_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Placeholder -->
            <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory scrollbar-hide -mx-6 px-6 md:mx-0 md:px-0">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="snap-center shrink-0 w-[300px] md:w-[380px] bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative group transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                        <svg class="w-10 h-10 text-indigo-50 absolute top-8 right-8 z-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                        </svg>

                        <div class="relative z-10 flex flex-col h-full">
                            <p class="text-base text-slate-700 leading-relaxed italic mb-8 flex-grow">
                                "Kualitas produk sangat memuaskan, bordir rapi dan kain terasa premium. Sangat merekomendasikan untuk pembuatan seragam skala besar."
                            </p>
                            
                            <div class="flex items-center gap-4 mt-auto">
                                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center font-bold text-white text-lg shadow-lg shadow-indigo-600/30 shrink-0 uppercase">
                                    {{ chr(64 + $i) }}
                                </div>
                                <div>
                                    <h5 class="font-outfit text-base font-black text-slate-900 line-clamp-1">Klien {{ $i }}</h5>
                                    <p class="text-slate-500 text-xs font-medium line-clamp-1">Perusahaan Bintang {{ $i }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>

</section>