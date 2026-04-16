<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - KonveksiHub</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased font-sans bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <x-layouts.header />

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10 flex-grow w-full pt-24" x-data="checkout()">
        <h1 class="text-2xl font-bold text-gray-900 mb-8 tracking-tight">Checkout</h1>

        <form action="{{ route('checkout.store') }}" method="POST" @submit.prevent="submitCheckout">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Left Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Customer Information Section -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">1. Customer Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="phone" name="phone_number" required placeholder="08..." class="flex-1 w-full h-10 px-3 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
                                    <button type="button" @click="checkAccount" :disabled="isLoading" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-bold rounded-lg border border-indigo-200 transition-colors whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Cek Akun
                                    </button>
                                </div>
                                <p x-show="message" x-text="message" class="text-xs mt-1" :class="isFound ? 'text-green-600 font-medium' : 'text-blue-600 font-medium'"></p>
                            </div>
                            
                            <div x-show="checked" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Customer Name</label>
                                <input type="text" x-model="name" name="client_name" required :readonly="isReadonly" :class="isReadonly ? 'bg-gray-50 text-gray-500 border-gray-200' : 'bg-white text-gray-900 border-gray-300'" class="w-full h-10 px-3 text-sm rounded-lg border focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition pointer-events-auto">
                                <p x-show="isFound" class="text-[10px] text-gray-400 mt-1 italic">*Nama sudah terdaftar di sistem kami</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary Section -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">2. Order Summary</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs uppercase bg-gray-50 text-gray-500">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-3 py-3">Variant/Size</th>
                                        <th class="px-3 py-3 text-center">Qty</th>
                                        <th class="px-3 py-3 text-right">Price</th>
                                        <th class="px-3 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($cart as $id => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $item['product_name'] }}</div>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="text-gray-600">{{ $item['variant_name'] }}</div>
                                                <div class="text-xs text-gray-500">Size {{ $item['size_name'] }}</div>
                                            </td>
                                            <td class="px-3 py-3 text-center">{{ $item['quantity'] }}</td>
                                            <td class="px-3 py-3 text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                            <td class="px-3 py-3 text-right font-medium">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right Content -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                        <h3 class="text-base font-extrabold text-gray-900 mb-5 pb-4 border-b border-gray-100">Ringkasan Belanja</h3>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Harga</span>
                                <span class="text-gray-900 font-medium">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Diskon</span>
                                <span class="text-emerald-500 font-medium">- Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-5 flex justify-between items-center mb-8">
                            <span class="text-base font-bold text-gray-900">Grand Total</span>
                            <span class="text-xl font-extrabold text-indigo-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                        
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-2">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </main>

    <footer class="bg-white border-t border-gray-200 py-8 text-center text-gray-500 text-sm mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            &copy; {{ date('Y') }} KonveksiHub. Hak Cipta Dilindungi.
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkout', () => ({
                clients: @json($clients),
                phone: '',
                name: '',
                isReadonly: false,
                isFound: false,
                message: '',
                checked: false,
                isLoading: false,

                checkAccount() {
                    let searchPhone = this.phone.trim();
                    if(!searchPhone) {
                        this.message = 'Silakan masukkan nomor telepon.';
                        this.isFound = false;
                        this.isReadonly = false;
                        return;
                    }

                    this.isLoading = true;
                    this.message = 'Sedang mengecek...';

                    // Simulate slight delay for better UX
                    setTimeout(() => {
                        let found = this.clients.find(c => c.phone_number === searchPhone);
                        this.checked = true;
                        if (found) {
                            this.name = found.client_name;
                            this.isReadonly = true;
                            this.isFound = true;
                            this.message = 'Akun ditemukan! Selamat datang kembali, ' + found.client_name;
                        } else {
                            this.name = '';
                            this.isReadonly = false;
                            this.isFound = false;
                            this.message = 'Akun tidak ditemukan. Silakan lengkapi data Anda.';
                            // Focusing input logic
                            setTimeout(() => document.querySelector('input[name="client_name"]').focus(), 100);
                        }
                        this.isLoading = false;
                    }, 600);
                },

                submitCheckout(e) {
                    if (!this.checked) {
                        alert('Silakan Cek Akun terlebih dahulu!');
                        return;
                    }
                    if (!this.phone.trim() || !this.name.trim()) {
                        alert('Mohon lengkapi Phone Number dan Customer Name!');
                        return;
                    }
                    e.target.submit();
                }
            }))
        })
    </script>
</body>
</html>
