<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - Konveksi hub</title>
    @php $appearance = \App\Models\AppearanceSetting::first(); @endphp
    <link rel="icon" type="image/png" href="{{ $appearance && $appearance->favicon ? asset('storage/' . $appearance->favicon) : asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>

<body class="antialiased bg-slate-50 text-slate-900">
    <x-layouts.header />

    <main class="pt-32 pb-24">
        <div class="container mx-auto px-6 max-w-4xl">
            <h1 class="font-outfit text-4xl md:text-5xl font-black text-slate-900 mb-8 uppercase tracking-tight">Privacy Policy</h1>
            
            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-200 prose prose-slate prose-indigo max-w-none">
                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        1. Introduction
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Selamat datang di Konveksi hub. Kami menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat Anda menggunakan situs web kami.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        2. Data Collection
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Kami mengumpulkan informasi yang Anda berikan langsung kepada kami, seperti saat Anda melakukan pemesanan, mendaftar akun, atau menghubungi kami. Informasi ini dapat mencakup nama, alamat email, nomor telepon, dan detail pengiriman.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        3. Data Usage
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Data yang kami kumpulkan digunakan untuk memproses pesanan Anda, memberikan layanan pelanggan, mengirimkan pembaruan tentang pesanan, dan meningkatkan pengalaman Anda di situs web kami. Kami tidak akan menjual informasi Anda kepada pihak ketiga.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        4. Cookies
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Situs web kami menggunakan cookies untuk meningkatkan navigasi dan memahami bagaimana pengunjung berinteraksi dengan konten kami. Anda dapat mengatur browser Anda untuk menolak cookies, namun ini mungkin mempengaruhi fungsi situs web.
                    </p>
                </section>

                <section>
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        5. Contact
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami melalui email di <span class="font-bold text-indigo-600">hello@konveksihub.com</span> atau WhatsApp di <span class="font-bold text-indigo-600">+62 819-0766-6620</span>.
                    </p>
                </section>
            </div>
        </div>
    </main>

    <x-layouts.footer />
</body>
</html>
