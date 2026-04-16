<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout Sukses - KonveksiHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased font-sans bg-gray-50 text-gray-900 min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 text-center border border-gray-100">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
            <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Order Berhasil!</h2>
        <p class="text-gray-500 mb-8">Terima kasih atas pesanan Anda, {{ session('checkout_success_name') }}.</p>

        <div class="bg-gray-50 rounded-2xl p-6 text-left mb-8 border border-gray-200">
            <h3 class="text-sm font-bold tracking-wider text-gray-500 uppercase mb-4 text-center">Rincian Transaksi</h3>

            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-600">ID Transaksi</span>
                <span class="font-mono font-medium text-gray-900">{{ session('checkout_success_trx_id') }}</span>
            </div>

            <div class="flex justify-between items-center mb-5">
                <span class="text-gray-600">Total Pembayaran</span>
                <span class="text-lg font-bold text-indigo-600">Rp
                    {{ number_format(session('checkout_success_total'), 0, ',', '.') }}</span>
            </div>

            <div class="border-t border-gray-200 pt-5 text-center mt-2">
                <p class="text-xs text-gray-500 mb-2 uppercase font-bold tracking-wider">Instruksi Pembayaran</p>
                <p class="font-medium text-gray-900">Transfer ke: Bank BCA</p>
                <p class="text-xl font-mono font-bold tracking-widest text-gray-900 my-1">123456789</p>
                <p class="text-sm text-gray-500">a/n PT KonveksiHub Example</p>
            </div>
        </div>

        @php
            $trxId = session('checkout_success_trx_id');
            $invoiceUrl = url('/invoice/' . $trxId);
            $phone = "6285669844179"; // Ganti dengan nomor WhatsApp admin valid
            $message = urlencode("Saya sudah melakukan pesanan dengan invoice berikut: " . $invoiceUrl);
            $whatsappUrl = "https://wa.me/{$phone}?text=" . $message;
        @endphp

        <div class="flex flex-col gap-3">
            <a href="{{ $invoiceUrl }}" target="_blank" rel="noopener noreferrer"
                class="w-full flex items-center justify-center px-8 py-3.5 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all duration-200">
                Lihat Resi
            </a>

            <a href="{{ $whatsappUrl }}" target="_blank"
                class="w-full flex items-center justify-center px-8 py-3.5 border border-transparent text-sm font-bold rounded-xl text-white bg-emerald-500 hover:bg-emerald-600 shadow-sm transition-all duration-200 gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
                Chat Admin
            </a>

            <a href="{{ url('/') }}"
                class="w-full flex items-center justify-center px-8 py-3.5 border border-gray-200 text-sm font-bold rounded-xl text-gray-600 bg-white hover:bg-gray-50 transition-all duration-200 mt-2">
                Kembali ke Homepage
            </a>
        </div>
    </div>
</body>

</html>