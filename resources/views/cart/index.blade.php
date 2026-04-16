<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - KonveksiHub</title>
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

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10 flex-grow w-full pt-24">
        <h1 class="text-2xl font-bold text-gray-900 mb-8 tracking-tight">Keranjang Belanja</h1>

        <div id="cart-container">
            @if(empty($cart))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-200 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900">Keranjangmu kosong</h2>
                    <p class="text-gray-500 mt-2 mb-8">Yuk, cari pakaian konveksi terbaik untukmu!</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-md hover:bg-indigo-700 transition">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cart as $id => $item)
                            <div id="cart-item-{{ str_replace('-', '', $id) }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 sm:p-6 flex flex-col sm:flex-row gap-6 relative group transform transition">
                                <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden shrink-0 flex items-center justify-center">
                                    @if($item['image'])
                                        <img src="{{ Storage::url($item['image']) }}" class="w-full h-full object-contain">
                                    @else
                                        <img src="https://placehold.co/200x200/f3f4f6/94a3b8?text=Produk" class="w-full h-full object-contain">
                                    @endif
                                </div>
                                
                                <div class="flex-grow flex flex-col min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="text-base font-bold text-gray-900 truncate pr-8">{{ $item['product_name'] }}</h3>
                                        <button onclick="removeItem('{{ $id }}')" class="absolute top-4 right-4 sm:top-6 sm:right-6 text-gray-400 hover:text-rose-500 transition-colors p-1" title="Hapus Item">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-xs text-gray-500 mb-3 space-x-3">
                                        <span>Warna: <span class="text-gray-900 font-medium">{{ $item['variant_name'] }}</span></span>
                                        <span>Ukuran: <span class="text-gray-900 font-medium">{{ $item['size_name'] }}</span></span>
                                    </div>
                                    
                                    <div class="mt-auto flex justify-between items-end gap-4">
                                        <div>
                                            <span class="text-base font-extrabold text-indigo-600 block">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                                            <span id="subtotal-{{ str_replace('-', '', $id) }}" class="text-[10px] text-gray-400">Total: Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                        </div>
                                        
                                        <div class="flex items-center border border-gray-300 rounded-lg h-9 overflow-hidden bg-white shadow-sm">
                                            <button type="button" 
                                                onclick="updateQty('{{ $id }}', -1)"
                                                class="w-9 h-full flex items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-50 border-r border-gray-300 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <input type="text" 
                                                id="qty-{{ str_replace('-', '', $id) }}" 
                                                value="{{ $item['quantity'] }}"
                                                class="w-10 h-full text-center text-sm font-bold text-gray-800 focus:outline-none pointer-events-none" 
                                                readonly>
                                            <button type="button" 
                                                onclick="updateQty('{{ $id }}', 1, {{ $item['max_stock'] }})"
                                                class="w-9 h-full flex items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-50 border-l border-gray-300 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                            <h3 class="text-base font-extrabold text-gray-900 mb-5 pb-4 border-b border-gray-100">Ringkasan Belanja</h3>
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Total Harga (<span id="summary-count">{{ count($cart) }}</span> item)</span>
                                    <span id="summary-total" class="text-gray-900 font-medium">Rp{{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Total Diskon</span>
                                    <span class="text-emerald-500 font-medium">- Rp0</span>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 pt-5 flex justify-between items-center mb-8">
                                <span class="text-base font-bold text-gray-900">Total Harga</span>
                                <span id="final-total" class="text-xl font-extrabold text-indigo-600">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            
                            <a href="{{ route('checkout.index') }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition duration-200 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                Pesan Sekarang
                            </a>
                            <p class="text-[10.5px] text-gray-400 mt-4 text-center">Lanjutkan untuk pembayaran dan pengiriman.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-8 text-center text-gray-500 text-sm mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            &copy; 2026 KonveksiHub. Hak Cipta Dilindungi.
        </div>
    </footer>

    <script>
        function updateQty(cartId, delta, max) {
            const safeId = cartId.replace('-', '');
            const qtyInput = document.getElementById('qty-' + safeId);
            let newQty = parseInt(qtyInput.value) + delta;

            if (newQty < 1) return;
            if (max && newQty > max) {
                alert('Stok tidak mencukupi');
                return;
            }

            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ cart_id: cartId, quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    qtyInput.value = newQty;
                    document.getElementById('subtotal-' + safeId).innerText = 'Total: ' + data.subtotal;
                    updateSummary(data);
                }
            });
        }

        function removeItem(cartId) {
            if (!confirm('Hapus item ini dari keranjang?')) return;

            const safeId = cartId.replace('-', '');
            const itemEl = document.getElementById('cart-item-' + safeId);
            
            itemEl.style.opacity = '0.5';
            itemEl.style.transform = 'scale(0.95)';

            fetch('{{ route('cart.remove') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ cart_id: cartId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    itemEl.remove();
                    updateSummary(data);
                    if (data.is_empty) {
                        location.reload();
                    }
                }
            });
        }

        function updateSummary(data) {
            document.getElementById('summary-total').innerText = data.total_price;
            document.getElementById('final-total').innerText = data.total_price;
            document.getElementById('summary-count').innerText = data.total_items;
            document.getElementById('nav-cart-count').innerText = data.total_items;
            
            if (data.total_items === 0) {
                document.getElementById('nav-cart-count').classList.add('hidden');
            } else {
                document.getElementById('nav-cart-count').classList.remove('hidden');
            }
        }


    </script>
</body>
</html>
