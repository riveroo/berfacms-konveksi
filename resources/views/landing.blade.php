<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konveksi hub | Solusi Apparel & Seragam Premium</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- SEO -->
    <meta name="description"
        content="Konveksi hub adalah mitra terpercaya untuk pembuatan seragam kantor, polo shirt, jaket, dan apparel premium dengan kualitas terbaik dan pengiriman tepat waktu.">

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

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-gradient {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.7) 100%);
        }
    </style>
</head>

<body class="antialiased bg-slate-50 text-slate-900" x-data="{ scrolled: false, mobileMenu: false }"
    @scroll.window="scrolled = (window.pageYOffset > 50)">

    <x-layouts.header />

    <!-- Hero Section -->
    <section id="beranda" class="relative min-h-screen flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1556905055-8f358a7a47b2?q=80&w=2070&auto=format&fit=crop"
                class="w-full h-full object-cover scale-105" alt="Convection Workshop">
            <div class="absolute inset-0 hero-gradient"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 pt-20">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-xs font-bold tracking-widest uppercase mb-8 animate-bounce">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Professional Apparel Manufacturer
                </div>
                <h1 class="font-outfit text-4xl md:text-5xl lg:text-7xl font-black text-white leading-tight mb-8">
                    MITRA KONVEKSI TERPERCAYA UNTUK <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">SERAGAM &
                        APPAREL PREMIUM.</span>
                </h1>
                <p class="text-xl md:text-2xl text-slate-300 leading-relaxed mb-12 max-w-2xl font-light">
                    Wujudkan desain Anda dengan kualitas terbaik, tepat waktu, dan harga kompetitif. Standar produksi
                    industri untuk brand global.
                </p>
                <div class="flex flex-col sm:flex-row gap-6">
                    <x-button variant="indigo" size="lg" href="{{ route('products.index') }}"
                        class="h-16 px-10 rounded-2xl text-lg shadow-2xl shadow-indigo-600/40 transform hover:-translate-y-1">
                        BELI SEKARANG
                    </x-button>
                    <x-button variant="outline" size="lg" href="{{ route('public.stock') }}"
                        class="h-16 px-10 rounded-2xl text-lg bg-white/5 border-white/20 text-white hover:bg-white/10 backdrop-blur-md transform hover:-translate-y-1">
                        CEK STOCK
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Decorative element -->
        <div
            class="absolute bottom-20 right-0 w-1/3 h-px bg-gradient-to-l from-indigo-500 scale-x-150 rotate-45 transform origin-right opacity-30">
        </div>
    </section>

    <!-- Why Choose Us -->
    <section id="layanan" class="py-32 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-24">
                <span class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-4 block">Our Values</span>
                <h2 class="font-outfit text-4xl md:text-6xl font-black text-slate-900 mb-6">Kenapa Memilih Kami?</h2>
                <div class="w-24 h-2 bg-indigo-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Card 1 -->
                <div
                    class="group p-10 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2">
                    <div
                        class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-8 transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-4">DESAIN CUSTOM</h3>
                    <p class="text-slate-600 leading-relaxed">Bebas buat desain sendiri dengan bantuan tim ahli kami.
                        Kami memandu Anda dari sketsa hingga sampel fisik produk.</p>
                </div>

                <!-- Card 2 -->
                <div
                    class="group p-10 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2">
                    <div
                        class="w-20 h-20 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-8 transition-transform duration-500 group-hover:-rotate-12 group-hover:scale-110">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-4">KUALITAS TERJAMIN</h3>
                    <p class="text-slate-600 leading-relaxed">Bahan pilihan kualitas ekspor dengan teknik pengerjaan
                        presisi. Quality Control berlapis di setiap tahap produksi.</p>
                </div>

                <!-- Card 3 -->
                <div
                    class="group p-10 rounded-3xl bg-slate-50 border border-slate-100 transition-all duration-500 hover:bg-white hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2">
                    <div
                        class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-8 transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-4">TEPAT WAKTU</h3>
                    <p class="text-slate-600 leading-relaxed">Jaminan ketepatan jadwal produksi dan rute distribusi.
                        Kecepatan tanpa mengorbankan kualitas adalah prinsip kami.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portofolio" class="py-32 bg-slate-900 overflow-hidden relative">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8">
                <div>
                    <span
                        class="text-indigo-400 font-bold uppercase tracking-widest text-sm mb-4 block">Portfolio</span>
                    <h2 class="font-outfit text-4xl md:text-6xl font-black text-white">HASIL KARYA TERBARU</h2>
                </div>
                <!-- Search bar optional as per req -->
                <div class="relative w-full lg:w-96">
                    <input type="text" placeholder="Search product..."
                        class="w-full bg-slate-800 border-none rounded-2xl py-5 px-6 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-indigo-600 rounded-xl text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Portfolio 1 -->
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden cursor-pointer"
                    x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <img src="/portfolio/office-shirt.png"
                        class="w-full h-full object-cover transition-transform duration-700"
                        :class="hover ? 'scale-110' : 'scale-100'" alt="Office Shirt">
                    <div class="absolute inset-0 bg-gradient-to-t from-indigo-900/90 via-indigo-900/20 to-transparent transition-opacity duration-500"
                        :class="hover ? 'opacity-100' : 'opacity-60'"></div>
                    <div class="absolute bottom-8 left-8 right-8 text-white transition-transform duration-500"
                        :class="hover ? '-translate-y-2' : 'translate-y-0'">
                        <p class="text-indigo-400 font-bold text-xs uppercase tracking-widest mb-2">Office Shirt</p>
                        <h4 class="font-outfit text-2xl font-black leading-tight">Formal Office Shirt</h4>
                    </div>
                </div>

                <!-- Portfolio 2 -->
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden cursor-pointer"
                    x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <img src="https://images.unsplash.com/photo-1581655353564-df123a1eb820?q=80&w=1887&auto=format&fit=crop"
                        class="w-full h-full object-cover transition-transform duration-700"
                        :class="hover ? 'scale-110' : 'scale-100'" alt="Polo Shirt">
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-blue-900/20 to-transparent transition-opacity duration-500"
                        :class="hover ? 'opacity-100' : 'opacity-60'"></div>
                    <div class="absolute bottom-8 left-8 right-8 text-white transition-transform duration-500"
                        :class="hover ? '-translate-y-2' : 'translate-y-0'">
                        <p class="text-blue-400 font-bold text-xs uppercase tracking-widest mb-2">Polo Shirt</p>
                        <h4 class="font-outfit text-2xl font-black leading-tight">Durable Polo Shirt</h4>
                    </div>
                </div>

                <!-- Portfolio 3 -->
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden cursor-pointer"
                    x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?q=80&w=1935&auto=format&fit=crop"
                        class="w-full h-full object-cover transition-transform duration-700"
                        :class="hover ? 'scale-110' : 'scale-100'" alt="Corporate Jacket">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent transition-opacity duration-500"
                        :class="hover ? 'opacity-100' : 'opacity-60'"></div>
                    <div class="absolute bottom-8 left-8 right-8 text-white transition-transform duration-500"
                        :class="hover ? '-translate-y-2' : 'translate-y-0'">
                        <p class="text-indigo-400 font-bold text-xs uppercase tracking-widest mb-2">Jacket</p>
                        <h4 class="font-outfit text-2xl font-black leading-tight">Corporate Jacket</h4>
                    </div>
                </div>

                <!-- Portfolio 4 -->
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden cursor-pointer"
                    x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <img src="/portfolio/tote-bag.png"
                        class="w-full h-full object-cover transition-transform duration-700"
                        :class="hover ? 'scale-110' : 'scale-100'" alt="Tote Bag">
                    <div class="absolute inset-0 bg-gradient-to-t from-cyan-900/90 via-cyan-900/20 to-transparent transition-opacity duration-500"
                        :class="hover ? 'opacity-100' : 'opacity-60'"></div>
                    <div class="absolute bottom-8 left-8 right-8 text-white transition-transform duration-500"
                        :class="hover ? '-translate-y-2' : 'translate-y-0'">
                        <p class="text-cyan-400 font-bold text-xs uppercase tracking-widest mb-2">Tote Bag</p>
                        <h4 class="font-outfit text-2xl font-black leading-tight">Branded Tote Bag</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Clients Section -->
    <section class="py-20 bg-slate-50 border-y border-slate-200 overflow-hidden">
        <div class="container mx-auto px-6 mb-12">
            <h3 class="text-center font-bold text-slate-400 uppercase tracking-widest text-sm">KLIEN KAMI</h3>
        </div>
        <div class="flex items-center gap-20 animate-infinite-scroll whitespace-nowrap">
            @for ($i = 0; $i < 4; $i++)
                <div
                    class="flex items-center gap-20 grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                    <span class="text-3xl font-black tracking-tighter">CLIENT LOGO 1</span>
                    <span class="text-3xl font-black tracking-tighter">CLIENT LOGO 2</span>
                    <span class="text-3xl font-black tracking-tighter">CLIENT LOGO 3</span>
                    <span class="text-3xl font-black tracking-tighter">CLIENT LOGO 4</span>
                    <span class="text-3xl font-black tracking-tighter">CLIENT LOGO 5</span>
                </div>
            @endfor
        </div>
    </section>

    <!-- Testimonials -->
    <section id="tentang" class="py-32 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="mb-24">
                <span class="text-indigo-600 font-bold uppercase tracking-widest text-sm mb-4 block">Testimonial</span>
                <h2 class="font-outfit text-4xl md:text-6xl font-black text-slate-900">YANG MEREKA KATAKAN</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Testimo 1 -->
                <div
                    class="bg-slate-50 p-12 rounded-[3rem] border border-slate-100 relative group transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2">
                    <svg class="w-16 h-16 text-indigo-100 absolute top-10 right-10" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                    <p class="text-2xl font-medium text-slate-800 leading-relaxed mb-10 italic">"Pengerjaan sangat
                        memuaskan, kualitas sablon awet meskipun sudah dicuci berkali-kali. Recommended!"</p>
                    <div class="flex items-center gap-6">
                        <div
                            class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center font-bold text-white text-2xl">
                            P</div>
                        <div>
                            <h5 class="font-outfit text-xl font-black">Nama Pelanggan</h5>
                            <p class="text-slate-500">Perusahaan A</p>
                        </div>
                    </div>
                </div>

                <!-- Testimo 2 -->
                <div
                    class="bg-slate-50 p-12 rounded-[3rem] border border-slate-100 relative group transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2">
                    <svg class="w-16 h-16 text-indigo-100 absolute top-10 right-10" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                    <p class="text-2xl font-medium text-slate-800 leading-relaxed mb-10 italic">"Ketepatan waktunya luar
                        biasa! Pesanan seragam kami selesai sebelum tenggat waktu yang ditentukan."</p>
                    <div class="flex items-center gap-6">
                        <div
                            class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center font-bold text-white text-2xl">
                            I</div>
                        <div>
                            <h5 class="font-outfit text-xl font-black">Nama Pelanggan</h5>
                            <p class="text-slate-500">Instansi B</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-32 relative overflow-hidden">
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="font-outfit text-5xl md:text-7xl font-black text-slate-900 mb-8 uppercase leading-tight">MULAILAH
                PESANAN ANDA<br><span class="text-indigo-600">SEKARANG!</span></h2>
            <div class="flex justify-center">
                <a href="https://wa.me/6281907666620"
                    class="inline-flex items-center gap-4 bg-[#25D366] hover:bg-[#1ebd5a] text-white px-10 py-6 rounded-3xl font-black text-2xl transition-all hover:scale-105 shadow-2xl shadow-green-500/20 active:scale-95">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.348-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    WHATSAPP KAMI
                </a>
            </div>
        </div>
        <!-- bg circles -->
        <div
            class="absolute top-1/2 left-0 w-[800px] h-[800px] bg-indigo-50 rounded-full -translate-x-1/2 -translate-y-1/2 -z-10">
        </div>
    </section>

    <!-- Footer -->
    <x-layouts.footer />

    <!-- CSS for infinite scroll -->
    <style>
        @keyframes infinite-scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .animate-infinite-scroll {
            animation: infinite-scroll 40s linear infinite;
        }
    </style>
</body>

</html>