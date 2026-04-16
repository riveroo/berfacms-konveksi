<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konveksi hub - Mitra Konveksi Terpercaya</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 selection:bg-indigo-500 selection:text-white" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navigation -->
    <header 
        class="fixed w-full top-0 z-50 transition-all duration-300"
        :class="{ 'bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200 py-3': scrolled, 'bg-transparent py-5': !scrolled }">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">K</div>
                    <span class="font-outfit font-extrabold text-xl tracking-tight" :class="{'text-gray-900': scrolled, 'text-white': !scrolled}">Konveksi hub</span>
                </a>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-8 font-medium text-sm transition-colors" :class="{'text-gray-600': scrolled, 'text-gray-200': !scrolled}">
                    <a href="#beranda" class="hover:text-indigo-500 transition">Beranda</a>
                    <a href="#layanan" class="hover:text-indigo-500 transition">Layanan</a>
                    <a href="#portofolio" class="hover:text-indigo-500 transition">Portofolio</a>
                    <a href="#tentang-kami" class="hover:text-indigo-500 transition">Tentang Kami</a>
                    <a href="#kontak" class="hover:text-indigo-500 transition">Kontak</a>
                </nav>

                <!-- CTA & Mobile Toggle -->
                <div class="flex items-center gap-4">
                    <x-button variant="indigo" href="#kontak" class="hidden md:flex shadow-indigo-500/30 shadow-lg hover:shadow-indigo-500/50">
                        MINTA PENAWARAN
                    </x-button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden" :class="{'text-gray-900': scrolled, 'text-white': !scrolled}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-cloak x-show="mobileMenuOpen" class="md:hidden absolute top-full left-0 w-full bg-white border-b border-gray-200 shadow-lg py-4 px-6 flex flex-col gap-4 text-sm font-medium">
            <a href="#beranda" @click="mobileMenuOpen = false" class="text-gray-700 hover:text-indigo-600">Beranda</a>
            <a href="#layanan" @click="mobileMenuOpen = false" class="text-gray-700 hover:text-indigo-600">Layanan</a>
            <a href="#portofolio" @click="mobileMenuOpen = false" class="text-gray-700 hover:text-indigo-600">Portofolio</a>
            <a href="#tentang-kami" @click="mobileMenuOpen = false" class="text-gray-700 hover:text-indigo-600">Tentang Kami</a>
            <a href="#kontak" @click="mobileMenuOpen = false" class="text-gray-700 hover:text-indigo-600">Kontak</a>
            <hr class="border-gray-100">
            <x-button variant="indigo" href="#kontak" class="justify-center">MINTA PENAWARAN</x-button>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="beranda" class="relative min-h-[90vh] flex items-center pt-20">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&q=80&w=2070" class="w-full h-full object-cover object-center" alt="Konveksi Workshop">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/95 via-gray-900/80 to-gray-900/40"></div>
        </div>

        <div class="container mx-auto px-6 max-w-7xl relative z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-semibold tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    PRODUKSI KUALITAS PREMIUM
                </div>
                <h1 class="font-outfit text-4xl md:text-6xl lg:text-7xl font-extrabold text-white leading-[1.1] mb-6">
                    MITRA KONVEKSI TERPERCAYA UNTUK <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">SERAGAM & APPAREL</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 md:leading-relaxed mb-10 max-w-2xl font-light">
                    Wujudkan desain Anda dengan kualitas terbaik, tepat waktu, dan harga kompetitif. Dedikasi penuh untuk setiap jahitan.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-button variant="blue" size="lg" href="#portofolio" class="justify-center shadow-blue-500/30 shadow-lg">
                        LIHAT KATALOG PRODUK
                    </x-button>
                    <x-button variant="outline" size="lg" href="#kontak" class="justify-center bg-transparent text-white border-white/30 hover:bg-white/10 hover:text-white backdrop-blur">
                        KONSULTASI GRATIS
                    </x-button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features / Why Choose Us -->
    <section id="layanan" class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <x-text variant="label" class="mb-2 text-indigo-600">Keunggulan Utama</x-text>
                <x-text variant="title" class="text-3xl md:text-4xl font-outfit">Kenapa Memilih Kami?</x-text>
                <x-text variant="muted" class="mt-4">Kami menghadirkan kombinasi sempurna antara estetika, fungsionalitas, dan ketahanan dalam setiap produk.</x-text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/30 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </div>
                    <x-text variant="heading" class="mb-3 font-outfit">DESAIN CUSTOM</x-text>
                    <x-text variant="muted" class="leading-relaxed">Bebas buat desain sendiri dengan bantuan tim ahli kami. Kami wujudkan visi brand Anda menjadi nyata.</x-text>
                </div>
                <!-- Feature 2 -->
                <div class="group p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/30 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <x-text variant="heading" class="mb-3 font-outfit">KUALITAS TERJAMIN</x-text>
                    <x-text variant="muted" class="leading-relaxed">Menggunakan bahan pilihan dan teknik pengerjaan rapi dengan standar kontrol kualitas yang amat ketat.</x-text>
                </div>
                <!-- Feature 3 -->
                <div class="group p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50/30 hover:shadow-xl hover:shadow-indigo-100 transition-all duration-300">
                    <div class="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center text-teal-600 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <x-text variant="heading" class="mb-3 font-outfit">PENGIRIMAN TEPAT WAKTU</x-text>
                    <x-text variant="muted" class="leading-relaxed">Jaminan ketepatan jadwal produksi dan rute distribusi untuk memastikan pesanan Anda tiba sesuai tenggat waktu.</x-text>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portofolio" class="py-24 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12">
                <div>
                    <x-text variant="label" class="mb-2 text-indigo-600">Showcase Produk</x-text>
                    <x-text variant="title" class="text-3xl md:text-4xl font-outfit">HASIL KARYA TERBARU</x-text>
                </div>
                
                <!-- Search feature as requested -->
                <div class="relative w-full md:w-64 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" placeholder="Search produk..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Item 1 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="aspect-[4/5] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1596755094514-f87e32f85e2c?auto=format&fit=crop&q=80&w=600" alt="Formal Office Shirt" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Office Shirt</p>
                        <h3 class="font-outfit font-bold text-gray-900 text-lg">Formal Office Shirt</h3>
                    </div>
                </div>
                <!-- Item 2 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="aspect-[4/5] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1581655353564-df123a1eb820?auto=format&fit=crop&q=80&w=600" alt="Durable Polo" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Polo Shirt</p>
                        <h3 class="font-outfit font-bold text-gray-900 text-lg">Durable Signature Polo</h3>
                    </div>
                </div>
                <!-- Item 3 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="aspect-[4/5] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&q=80&w=600" alt="Corporate Jacket" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Jacket</p>
                        <h3 class="font-outfit font-bold text-gray-900 text-lg">Corporate Executive Jacket</h3>
                    </div>
                </div>
                <!-- Item 4 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="aspect-[4/5] overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1597435165688-6c84c6c18cf5?auto=format&fit=crop&q=80&w=600" alt="Branded Tote Bag" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Accessories</p>
                        <h3 class="font-outfit font-bold text-gray-900 text-lg">Premium Branded Tote Bag</h3>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 text-center">
                <x-button variant="outline" href="#" class="px-8 border-gray-300 rounded-full">Lihat Semua Portofolio</x-button>
            </div>
        </div>
    </section>

    <!-- Clients / Brands Section -->
    <section class="py-16 bg-white border-y border-gray-100 overflow-hidden">
        <div class="container mx-auto px-6 max-w-7xl">
            <x-text variant="label" class="text-center mb-8 text-gray-400">KLIEN KAMI YANG TERPERCAYA</x-text>
            
            <!-- Simple infinite carousel using Alpine or just normal flex wrap -->
            <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="text-2xl font-black tracking-tighter text-gray-800">STARBUCKS<span class="text-indigo-600">.</span></div>
                <div class="text-2xl font-bold italic font-serif text-gray-800">Bank Central</div>
                <div class="text-2xl font-extrabold uppercase tracking-widest text-gray-800">MANDIRI</div>
                <div class="text-2xl font-semibold opacity-80 text-gray-800 tracking-tight lowercase">tech<span class="text-blue-500">corp</span></div>
                <div class="text-xl font-bold border-2 border-gray-800 px-2 py-1 text-gray-800">GO-JEK</div>
                <div class="text-2xl font-black italic text-gray-800">Pertamina</div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section id="tentang-kami" class="py-24 bg-gray-900 text-white relative overflow-hidden">
        <!-- Abstract gradient blob -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform -translate-x-1/2 translate-y-1/2"></div>
        
        <div class="container mx-auto px-6 max-w-7xl relative z-10">
            <div class="mb-16">
                <x-text variant="label" class="mb-2 text-indigo-400">Testimonial</x-text>
                <h2 class="text-3xl md:text-5xl font-outfit font-bold text-white leading-tight max-w-2xl">YANG MEREKA KATAKAN TENTANG KAMI</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Testimo 1 -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-colors">
                    <svg class="w-10 h-10 text-indigo-400 mb-6 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                    <p class="text-lg text-gray-300 leading-relaxed italic mb-8">"Pengerjaan sangat memuaskan, kualitas sablon awet meskipun sudah dicuci berkali-kali. Komunikasinya juga sangat ramah dan responsif."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center font-bold text-xl">D</div>
                        <div>
                            <div class="font-bold text-white font-outfit">Dimas Anggara</div>
                            <div class="text-sm text-indigo-300">Manager, Perusahaan A</div>
                        </div>
                    </div>
                </div>
                <!-- Testimo 2 -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-colors">
                    <svg class="w-10 h-10 text-indigo-400 mb-6 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                    <p class="text-lg text-gray-300 leading-relaxed italic mb-8">"Ketepatan waktunya luar biasa! Pesanan seragam pabrik kami selesai jauh sebelum tenggat waktu yang ditentukan dengan kualitas presisi."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center font-bold text-xl">S</div>
                        <div>
                            <div class="font-bold text-white font-outfit">Siti Juleha</div>
                            <div class="text-sm text-indigo-300">Kepala Pengadaan, Instansi B</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section id="kontak" class="py-24 bg-indigo-600 bg-cover bg-center" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        <div class="container mx-auto px-6 max-w-4xl text-center">
            <h2 class="text-4xl md:text-5xl font-outfit font-bold text-white mb-6">MULAILAH PESANAN ANDA SEKARANG!</h2>
            <p class="text-indigo-100 text-lg mb-10 max-w-2xl mx-auto">Tim ahli kami siap melayani pesanan kustom partai besar maupun menengah. Dapatkan penawaran harga terbaik hari ini.</p>
            
            <!-- WhatsApp Button Style -->
            <a href="#" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-[#25D366] hover:bg-[#1ebd5a] text-white font-bold rounded-full text-lg shadow-lg shadow-[#25D366]/30 hover:shadow-[#25D366]/50 transition-all transform hover:-translate-y-1">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                WHATSAPP KAMI
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-400 py-16 border-t border-gray-900">
        <div class="container mx-auto px-6 max-w-7xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                
                <!-- Brand Info -->
                <div class="lg:col-span-1">
                    <a href="#" class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">K</div>
                        <span class="font-outfit font-extrabold text-xl tracking-tight text-white">Konveksi hub</span>
                    </a>
                    <p class="text-sm leading-relaxed mb-6">Terpercaya mewujudkan ide desain Anda ke bentuk apparel berkualitas wahid. Pilihan terbaik instansi dan brand ternama.</p>
                    <!-- Socials -->
                    <div class="flex items-center gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="lg:col-span-1">
                    <h4 class="text-white font-bold mb-6 font-outfit uppercase tracking-wider text-sm">Navigasi Singkat</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#beranda" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#layanan" class="hover:text-white transition-colors">Layanan</a></li>
                        <li><a href="#portofolio" class="hover:text-white transition-colors">Katalog & Portofolio</a></li>
                        <li><a href="#tentang-kami" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="#kontak" class="hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="lg:col-span-1">
                    <h4 class="text-white font-bold mb-6 font-outfit uppercase tracking-wider text-sm">Kontak Kami</h4>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Gedung Konveksi Hub Lt. 2<br>Jl. Sudirman No 45, Jakarta 12190</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>+62 821-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>hello@konveksihub.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Map -->
                <div class="lg:col-span-1 border border-gray-800 rounded-xl overflow-hidden bg-gray-900 aspect-video lg:aspect-auto">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1m3!1d126920.240366627!2d106.7588383!3d-6.2297419!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x100c5e82dd4b820!2sJakarta%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1714571900000!5m2!1sid!2sid" width="100%" height="100%" style="border:0; filter: grayscale(1) opacity(0.8); pointer-events: none;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

            </div>

            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
                <p>Copyright &copy; {{ date('Y') }} All Rights Reserved by Konveksi hub.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
