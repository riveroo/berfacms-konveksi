@php
    $footer = \App\Models\LandingFooter::first();
    $companyName = $footer->company_name ?? 'Konveksi hub';
    $address = $footer->address ?? 'Jakarta';
    $phone = $footer->phone ?? '+62 819-0766-6620';
    $email = $footer->email ?? 'hello@konveksihub.com';
@endphp

<footer id="kontak" {{ $attributes->merge(['class' => 'bg-slate-950 pt-32 pb-12 text-slate-400']) }}>
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-20 mb-32">
            <div class="lg:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center gap-2 mb-10 group">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl transition-transform group-hover:scale-110 shadow-lg shadow-indigo-600/20">
                        {{ substr($companyName, 0, 1) }}
                    </div>
                    <span class="font-outfit text-2xl font-black tracking-tight text-white italic">{{ $companyName }}</span>
                </a>
                <p class="text-lg leading-relaxed mb-10">Mewujudkan desain Anda dengan presisi industri dan estetika modern. Partner resmi brand apparel dunia.</p>
                <div class="flex flex-wrap gap-4">
                    @if($footer)
                        @if($footer->instagram_url)
                            <a href="{{ $footer->instagram_url }}" target="_blank" class="hover:text-white transform hover:scale-110 transition-all">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                        @if($footer->facebook_url)
                            <a href="{{ $footer->facebook_url }}" target="_blank" class="hover:text-white transform hover:scale-110 transition-all">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </a>
                        @endif
                        @if($footer->youtube_url)
                            <a href="{{ $footer->youtube_url }}" target="_blank" class="hover:text-white transform hover:scale-110 transition-all">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                            </a>
                        @endif
                        @if($footer->tiktok_url)
                            <a href="{{ $footer->tiktok_url }}" target="_blank">
                                <img src="https://www.vectorlogo.zone/logos/tiktok/tiktok-icon.svg" alt="TikTok" class="w-6 h-6 object-contain grayscale hover:grayscale-0 transition">
                            </a>
                        @endif
                        @if($footer->tokopedia_url)
                            <a href="{{ $footer->tokopedia_url }}" target="_blank">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/v/v1/Tokopedia.svg" alt="Tokopedia" class="w-6 h-6 object-contain grayscale hover:grayscale-0 transition">
                            </a>
                        @endif
                        @if($footer->shopee_url)
                            <a href="{{ $footer->shopee_url }}" target="_blank">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Shopee.svg" alt="Shopee" class="w-6 h-6 object-contain grayscale hover:grayscale-0 transition">
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <div class="lg:col-span-1">
                <h4 class="font-outfit text-white text-xl font-black mb-10">CONTACT US</h4>
                <ul class="space-y-6">
                    @if($phone)
                    <li class="flex items-center gap-4 text-white">
                        <span class="p-3 bg-indigo-600 rounded-xl shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></span>
                        {{ $phone }}
                    </li>
                    @endif
                    @if($email)
                    <li class="flex items-center gap-4 text-white">
                        <span class="p-3 bg-indigo-600 rounded-xl shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></span>
                        {{ $email }}
                    </li>
                    @endif
                    @if($address)
                    <li class="flex items-start gap-4 text-white">
                        <span class="p-3 bg-indigo-600 rounded-xl mt-1 shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></span>
                        <span class="pt-1">{{ $address }}</span>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-[2rem] overflow-hidden grayscale hover:grayscale-0 transition-all duration-700 h-64 border border-slate-800">
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q={{ urlencode($address ?? 'Jakarta') }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                </div>
            </div>
        </div>

        <div class="pt-12 border-t border-slate-900 flex flex-col md:flex-row justify-between items-center gap-8 text-sm">
            <p>Copyright &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
            <div class="flex gap-8">
                <a href="{{ route('privacy') ?? '#' }}" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="{{ route('terms') ?? '#' }}" class="hover:text-white transition-colors">Terms of Use</a>
            </div>
        </div>
    </div>
</footer>
