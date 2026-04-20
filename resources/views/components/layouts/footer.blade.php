<footer id="kontak" {{ $attributes->merge(['class' => 'bg-slate-950 pt-32 pb-12 text-slate-400']) }}>
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-20 mb-32">
            <div class="lg:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center gap-2 mb-10 group">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl transition-transform group-hover:scale-110 shadow-lg shadow-indigo-600/20">
                        K
                    </div>
                    <span class="font-outfit text-2xl font-black tracking-tight text-white italic">Konveksi <span class="text-indigo-500">hub</span></span>
                </a>
                <p class="text-lg leading-relaxed mb-10">Mewujudkan desain Anda dengan presisi industri dan estetika modern. Partner resmi brand apparel dunia.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transform hover:scale-110 transition-all">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <!-- More social icons here if needed -->
                </div>
            </div>

            <div class="lg:col-span-1">
                <h4 class="font-outfit text-white text-xl font-black mb-10">CONTACT US</h4>
                <ul class="space-y-6">
                    <li class="flex items-center gap-4 text-white">
                        <span class="p-3 bg-indigo-600 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></span>
                        +62 819-0766-6620
                    </li>
                    <li class="flex items-center gap-4 text-white">
                        <span class="p-3 bg-indigo-600 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></span>
                        hello@konveksihub.com
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-[2rem] overflow-hidden grayscale hover:grayscale-0 transition-all duration-700 h-64 border border-slate-800">
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=Jakarta+(Konveksi%20Hub)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                </div>
            </div>
        </div>

        <div class="pt-12 border-t border-slate-900 flex flex-col md:flex-row justify-between items-center gap-8">
            <p>Copyright &copy; {{ date('Y') }} Konveksi hub. All rights reserved.</p>
            <div class="flex gap-8">
                <a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms of Use</a>
            </div>
        </div>
    </div>
</footer>
