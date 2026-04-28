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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

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
    </style>
</head>

<body class="antialiased bg-slate-50 text-slate-900">

    <!-- Header -->
    <x-layouts.header />

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <x-landing.hero />

        <!-- Client Logos -->
        <x-landing.client-logo />

        <!-- Our Values -->
        <x-landing.value />

        <!-- Product Categories -->
        <x-landing.category />

        <!-- Popular Products -->
        <x-landing.popular />

        <!-- Promotional Banner -->
        <x-landing.banner />

        <!-- Client Reviews -->
        <x-landing.review />
    </main>

    <!-- Footer -->
    <x-layouts.footer />

</body>
</html>