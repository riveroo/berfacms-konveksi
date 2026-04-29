    <!-- Product Category Section -->
    @php
        $setting = \App\Models\LandingSectionSetting::where('key', 'category_rows')->first();
        $rows = $setting ? (int) $setting->value : 1;
        $limit = $rows * 3;
        $categories = \App\Models\LandingCategory::orderBy('sort_order')->limit($limit)->get();
    @endphp

    @if($categories->count() > 0)
    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-12">
                <h2 class="font-outfit text-3xl md:text-5xl font-black text-slate-900">Product Categories</h2>
                <div class="w-16 h-1 bg-indigo-600 mt-4 rounded-full"></div>
            </div>

            <div class="flex gap-4 md:grid md:grid-cols-3 md:gap-8 overflow-x-auto md:overflow-visible pb-6 md:pb-0 snap-x snap-mandatory scrollbar-hide -mx-6 px-6 md:mx-0 md:px-0">
                @foreach($categories as $category)
                    <a href="{{ $category->link }}" class="snap-center shrink-0 w-[280px] md:w-auto group relative block aspect-[4/3] rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-xl transition-all duration-300 flex items-center justify-center p-4">
                        <img src="{{ asset('storage/' . $category->image) }}" class="max-w-full max-h-full object-contain transition-transform duration-700 group-hover:scale-110" alt="{{ $category->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 md:bottom-6 md:left-6 md:right-6 z-10">
                            <h3 class="font-outfit text-white text-lg md:text-2xl font-bold tracking-wide">{{ $category->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @else
    <section class="bg-slate-50 py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-12">
                <h2 class="font-outfit text-3xl md:text-5xl font-black text-slate-900">Product Categories</h2>
                <div class="w-16 h-1 bg-indigo-600 mt-4 rounded-full"></div>
            </div>

            <div class="flex gap-4 md:grid md:grid-cols-3 md:gap-8 overflow-x-auto md:overflow-visible pb-6 md:pb-0 snap-x snap-mandatory scrollbar-hide -mx-6 px-6 md:mx-0 md:px-0">
                @for ($i = 1; $i <= 3; $i++)
                    <a href="#" class="snap-center shrink-0 w-[280px] md:w-auto group relative block aspect-[4/3] rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-xl transition-all duration-300 flex items-center justify-center p-4">
                        <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=800&auto=format&fit=crop" class="max-w-full max-h-full object-contain transition-transform duration-700 group-hover:scale-110" alt="Category {{ $i }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 md:bottom-6 md:left-6 md:right-6 z-10">
                            <h3 class="font-outfit text-white text-lg md:text-2xl font-bold tracking-wide">Category {{ $i }}</h3>
                        </div>
                    </a>
                @endfor
            </div>
        </div>
    </section>
    @endif
