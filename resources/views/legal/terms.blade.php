<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Use - Konveksi hub</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

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
            <h1 class="font-outfit text-4xl md:text-5xl font-black text-slate-900 mb-8 uppercase tracking-tight">Terms of Use</h1>
            
            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-200 prose prose-slate prose-indigo max-w-none">
                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        1. Introduction
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Dengan mengakses dan menggunakan situs web Konveksi hub, Anda setuju untuk terikat oleh Syarat Penggunaan ini. Jika Anda tidak setuju dengan bagian mana pun dari syarat ini, Anda disarankan untuk tidak menggunakan layanan kami.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        2. User Responsibilities
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Anda bertanggung jawab untuk memberikan informasi yang akurat dan lengkap saat melakukan pemesanan. Anda setuju untuk menggunakan situs web ini hanya untuk tujuan yang sah dan tidak melanggar hak pihak lain.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        3. Orders & Payments
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Semua pesanan tunduk pada ketersediaan produk dan konfirmasi harga. Kami berhak untuk menolak pesanan apa pun. Pembayaran harus dilakukan melalui metode yang tersedia di situs web kami sebelum pesanan diproses.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        4. Limitations
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Konveksi hub tidak bertanggung jawab atas kerugian tidak langsung atau konsekuensial yang diakibatkan oleh penggunaan atau ketidakmampuan untuk menggunakan situs web atau layanan kami.
                    </p>
                </section>

                <section>
                    <h2 class="font-outfit text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-1 bg-indigo-600 rounded-full"></span>
                        5. Contact
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Untuk pertanyaan lebih lanjut mengenai Syarat Penggunaan kami, silakan hubungi tim administrasi kami melalui email di <span class="font-bold text-indigo-600">hello@konveksihub.com</span>.
                    </p>
                </section>
            </div>
        </div>
    </main>

    <x-layouts.footer />
</body>
</html>
