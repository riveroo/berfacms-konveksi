<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;

Route::get('/', function () {
    return view('landing');
});

Route::get('/privacy-policy', fn() => view('legal.privacy'))->name('privacy');
Route::get('/terms-of-use', fn() => view('legal.terms'))->name('terms');

use App\Http\Controllers\PublicStockController;
Route::get('/stock', [PublicStockController::class, 'index'])->name('public.stock');

Route::get('/products', function (Illuminate\Http\Request $request) {
    $sort = $request->query('sort', 'latest');
    $productsQuery = Product::with('variants.stocks.sizeOption')->where('is_active', true)->latest();

    $products = $productsQuery->get();

    if ($sort === 'price_asc') {
        $products = $products->sortBy(function ($product) {
            $prices = $product->variants->flatMap->stocks->pluck('price')->filter()->toArray();
            return count($prices) > 0 ? min($prices) : 0;
        });
    } elseif ($sort === 'price_desc') {
        $products = $products->sortByDesc(function ($product) {
            $prices = $product->variants->flatMap->stocks->pluck('price')->filter()->toArray();
            return count($prices) > 0 ? max($prices) : 0;
        });
    }

    return view('products.index', compact('products', 'sort'));
})->name('products.index');

Route::get('/products/{id}', function ($id) {
    $product = \App\Models\Product::with('variants.stocks.sizeOption')->where('is_active', true)->findOrFail($id);

    // Find first variant+size combination with stock > 0
    $firstAvailable = $product->variants->flatMap->stocks->where('stock', '>', 0)->first();
    
    $defaultVariantId = $firstAvailable ? $firstAvailable->variant_id : null;
    $defaultSizeId = $firstAvailable ? $firstAvailable->size_option_id : null;

    $otherProducts = \App\Models\Product::where('id', '!=', $product->id)
        ->where('is_active', true)
        ->latest()
        ->take(5)
        ->get();
    return view('products.show', compact('product', 'otherProducts', 'defaultVariantId', 'defaultSizeId'));
})->name('products.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// require __DIR__.'/auth.php';

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

use App\Http\Controllers\CheckoutController;
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/invoice/{trx_id}', [CheckoutController::class, 'invoice'])->name('invoice.show');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('switch.locale');

use App\Http\Controllers\CekStokController;
Route::middleware(['auth'])->group(function () {
    Route::get('/cek-stok/product', [CekStokController::class, 'product'])->name('cek-stok.product');
    Route::get('/cek-stok/export', [CekStokController::class, 'downloadTemplate'])->name('cek-stok.export');
    Route::post('/cek-stok/import', [CekStokController::class, 'importStock'])->name('cek-stok.import');
    Route::get('/cek-stok/barang', [CekStokController::class, 'barang'])->name('cek-stok.barang');
});

use App\Http\Controllers\TransactionController;
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/admin/transactions/report', [\App\Http\Controllers\SalesReportController::class, 'index'])->name('transactions.report');
    Route::get('/admin/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/admin/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/admin/transactions/{id}', [TransactionController::class, 'detail'])->name('transactions.detail');
    Route::post('/admin/transactions/{id}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::get('/admin/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/admin/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::post('/admin/transactions/{id}/payment', [TransactionController::class, 'inputPayment'])->name('transactions.payment');
    Route::post('/admin/transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('transactions.status');
});

use App\Http\Controllers\PreOrderController;
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/pre-orders', [PreOrderController::class, 'index'])->name('pre-orders.index');
    Route::get('/admin/pre-orders/create', [PreOrderController::class, 'create'])->name('pre-orders.create');
    Route::post('/admin/pre-orders/store', [PreOrderController::class, 'store'])->name('pre-orders.store');

    Route::get('/admin/pre-orders/{id}', [PreOrderController::class, 'detail'])->name('pre-orders.detail');
    Route::post('/admin/pre-orders/{id}/reject', [PreOrderController::class, 'reject'])->name('pre-orders.reject');
    Route::post('/admin/pre-orders/{id}/accept', [PreOrderController::class, 'accept'])->name('pre-orders.accept');

    Route::get('/admin/appearance', [\App\Http\Controllers\AppearanceController::class, 'index'])->name('admin.appearance.index');
    Route::post('/admin/appearance', [\App\Http\Controllers\AppearanceController::class, 'update'])->name('admin.appearance.update');

    Route::get('/coming-soon', function () {
        return view('coming-soon');
    })->name('coming-soon');

    Route::get('/inventory/overview', [\App\Http\Controllers\InventoryOverviewController::class, 'index'])->name('inventory.overview');
    Route::get('/inventory/overview/export', [\App\Http\Controllers\InventoryOverviewController::class, 'export'])->name('inventory.overview.export');
    Route::get('/inventory/overview/template', [\App\Http\Controllers\InventoryOverviewController::class, 'downloadTemplate'])->name('inventory.overview.template');
    Route::post('/inventory/overview/import', [\App\Http\Controllers\InventoryOverviewController::class, 'import'])->name('inventory.overview.import');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/fix-storage', function () {
        $shortcut = public_path('storage');
        
        // Jika sudah ada tapi berupa link rusak, hapus
        if (is_link($shortcut)) {
            @unlink($shortcut);
        }

        // Jika folder belum ada, buat foldernya secara fisik
        if (!file_exists($shortcut)) {
            mkdir($shortcut, 0755, true);
            return 'Physical storage folder created in public/storage. No symlink needed.';
        }

        return 'Public storage folder already exists.';
    })->name('admin.fix-storage');
});

Route::get('/migrate-database', function () {
    try {
        // Menjalankan migrasi dengan flag --force (wajib di production)
        Artisan::call('migrate', ['--force' => true]);

        return 'Migrasi Berhasil! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Terjadi Error: ' . $e->getMessage();
    }
});

Route::get('/build-assets', function () {
    try {
        // Set PATH agar npm bisa ditemukan di beberapa env hosting
        putenv('PATH=' . getenv('PATH') . ':/usr/local/bin:/usr/bin:/bin');
        
        // Jalankan npm run build dan tangkap outputnya
        $output = shell_exec('npm run build 2>&1');

        return 'Build Selesai! <br><pre>' . $output . '</pre>';
    } catch (\Exception $e) {
        return 'Terjadi Error saat Build: ' . $e->getMessage();
    }
});

Route::get('/run-seeder', function () {
    try {
        // Opsi A: Menjalankan DatabaseSeeder utama
        Artisan::call('db:seed', ['--force' => true]);

        // Opsi B: Jika ingin menjalankan seeder spesifik (hapus komentar di bawah jika perlu)
        // Artisan::call('db:seed', ['--class' => 'UserSeeder', '--force' => true]);

        return 'Seeding Berhasil! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Terjadi Error saat Seeding: ' . $e->getMessage();
    }
});

Route::fallback(function () {
    return response()->view('errors.custom', [
        'code' => 404,
        'exception' => new \Exception('We can\'t seem to find the page you\'re looking for.'),
    ], 404);
});
