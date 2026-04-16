<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $transaction->trx_id }} - KonveksiHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-white text-gray-900 min-h-screen py-8 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
        <!-- Minimal Info for Document -->
        <div class="flex justify-between items-end mb-8 border-b-2 border-indigo-600 pb-4">
            <div>
                <h1 class="text-3xl font-black text-indigo-600 tracking-tighter">Konveksi <span class="text-slate-900">hub</span></h1>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Premium Clothing & Convection</p>
            </div>
            <div class="text-right hidden sm:block">
                <button onclick="window.print()" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    CETAK RESI
                </button>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white overflow-hidden">
            <!-- Header -->
            <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Invoice</p>
                        @php
                            $statusClasses = match ($transaction->status) {
                                'paid' => 'bg-green-100 text-green-800',
                                'on progress' => 'bg-blue-100 text-blue-800',
                                'done' => 'bg-gray-100 text-gray-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase {{ $statusClasses }}">
                            {{ $transaction->status }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-mono font-bold text-gray-900">{{ $transaction->trx_id }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Diterbitkan: {{ $transaction->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Informasi Pelanggan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nama Pelanggan</p>
                        <p class="font-medium text-gray-900">{{ optional($transaction->client)->client_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                        <p class="font-medium text-gray-900">{{ optional($transaction->client)->phone_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="p-6 sm:p-8 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wide">Rincian Pesanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="pb-3 pr-4 font-semibold">Produk</th>
                                <th class="pb-3 px-3 font-semibold text-center">Jml</th>
                                <th class="pb-3 px-3 font-semibold text-right">Harga</th>
                                <th class="pb-3 px-3 font-semibold text-right">Diskon</th>
                                <th class="pb-3 pl-3 font-semibold text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transaction->details as $item)
                            <tr>
                                <td class="py-4 pr-4">
                                    <p class="font-medium text-gray-900">{{ optional($item->product)->product_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ optional($item->variant)->variant_name }} | Size {{ optional($item->sizeOption)->name }}</p>
                                </td>
                                <td class="py-4 px-3 text-center text-gray-900">{{ $item->quantity }}</td>
                                <td class="py-4 px-3 text-right text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-4 px-3 text-right text-rose-500">{{ $item->discount > 0 ? '-Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}</td>
                                <td class="py-4 pl-3 text-right font-medium text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Section -->
            <div class="p-6 sm:p-8 bg-white">
                <div class="max-w-xs ml-auto space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Harga</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Diskon</span>
                        <span class="font-medium text-rose-500">-Rp {{ number_format($transaction->total_discount, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-gray-900 uppercase text-xs tracking-wider">Grand Total</span>
                        <span class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Print / Action Footer -->
        <div class="mt-12 text-center text-xs text-gray-400 border-t border-gray-100 pt-6">
            <p>Terima kasih telah mempercayakan kebutuhan konveksi Anda kepada <strong>Konveksi hub</strong>.</p>
            <p class="mt-1 italic">Simpan resi ini sebagai bukti transaksi yang sah.</p>
        </div>
        
    </div>
</body>
</html>
