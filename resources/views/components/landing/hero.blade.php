    <section id="beranda" class="relative w-full aspect-[16/9] md:aspect-[24/10] overflow-hidden bg-slate-900">
        <div class="absolute inset-0 z-0 w-full h-full" 
            x-data='{ 
                current: 0, 
                slides: {!! \App\Models\LandingHero::where("is_active", true)->orderBy("sort_order")->limit(5)->get()->map(fn($h) => ["image" => asset("storage/" . $h->image), "link" => $h->link])->toJson() !!} 
            }' 
            x-init="if (slides.length > 1) { setInterval(() => { current = (current + 1) % slides.length }, 5000) }">
            
            <!-- Carousel Images -->
            <template x-if="slides.length > 0">
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="current === index" 
                        x-transition:enter="transition ease-out duration-1000"
                        x-transition:enter-start="opacity-0 scale-105"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-1000"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-105"
                        class="absolute inset-0 w-full h-full">
                        <template x-if="slide.link">
                            <a :href="slide.link" class="block w-full h-full cursor-pointer">
                                <img :src="slide.image" class="w-full h-full object-contain md:object-cover" alt="Hero Banner">
                            </a>
                        </template>
                        <template x-if="!slide.link">
                            <img :src="slide.image" class="w-full h-full object-contain md:object-cover" alt="Hero Banner">
                        </template>
                    </div>
                </template>
            </template>
            
            <!-- Fallback Image -->
            <template x-if="slides.length === 0">
                <img src="https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=2070&auto=format&fit=crop"
                    class="absolute inset-0 w-full h-full object-cover" alt="Convection Workshop">
            </template>

            <!-- Navigation Arrows (Optional) -->
            <template x-if="slides.length > 1">
                <div class="absolute inset-0 flex items-center justify-between px-4 sm:px-8 z-10 pointer-events-none">
                    <button @click="current = current === 0 ? slides.length - 1 : current - 1" 
                        class="w-10 h-10 md:w-14 md:h-14 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md flex items-center justify-center text-white transition pointer-events-auto shadow-lg">
                        <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="current = (current + 1) % slides.length" 
                        class="w-10 h-10 md:w-14 md:h-14 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur-md flex items-center justify-center text-white transition pointer-events-auto shadow-lg">
                        <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </template>

            <!-- Navigation Dots -->
            <template x-if="slides.length > 1">
                <div class="absolute bottom-6 md:bottom-10 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="current = index"
                            class="rounded-full transition-all duration-300 shadow-sm"
                            :class="current === index ? 'w-8 h-2.5 bg-white' : 'w-2.5 h-2.5 bg-white/50 hover:bg-white/80'">
                        </button>
                    </template>
                </div>
            </template>
        </div>
    </section>
