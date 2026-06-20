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
    $productsQuery = Product::with('variants.stocks.sizeOption')
        ->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('is_service')->orWhere('is_service', '!=', 'yes');
        })
        ->orderBy('sort_order', 'asc')
        ->orderBy('created_at', 'desc');

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
    $product = \App\Models\Product::with('variants.stocks.sizeOption')
        ->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('is_service')->orWhere('is_service', '!=', 'yes');
        })
        ->findOrFail($id);

    // Find first variant+size combination with stock > 0
    $firstAvailable = $product->variants->flatMap->stocks->where('stock', '>', 0)->first();
    
    $defaultVariantId = $firstAvailable ? $firstAvailable->variant_id : null;
    $defaultSizeId = $firstAvailable ? $firstAvailable->size_option_id : null;

    $otherProducts = \App\Models\Product::where('id', '!=', $product->id)
        ->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('is_service')->orWhere('is_service', '!=', 'yes');
        })
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
        session()->save();
    }
    return redirect()->back();
})->name('switch.locale');

use App\Http\Controllers\CekStokController;
use App\Http\Controllers\ProductPricingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PreOrderController;

Route::middleware([
    \Filament\Http\Middleware\SetUpPanel::class . ':admin',
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Filament\Http\Middleware\AuthenticateSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \Filament\Http\Middleware\DisableBladeIconComponents::class,
    \App\Http\Middleware\SetLanguage::class,
    \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
    \App\Http\Middleware\CheckMenuPermission::class,
    'auth',
])->group(function () {
    Route::get('/cek-stok/product', [CekStokController::class, 'product'])->name('cek-stok.product');
    Route::get('/cek-stok/export', [CekStokController::class, 'downloadTemplate'])->name('cek-stok.export');
    Route::post('/cek-stok/import', [CekStokController::class, 'importStock'])->name('cek-stok.import');
    Route::get('/cek-stok/barang', [CekStokController::class, 'barang'])->name('cek-stok.barang');

    Route::get('/admin/product-pricing', [ProductPricingController::class, 'index'])->name('admin.product-pricing');
    Route::post('/admin/product-pricing/update/{id}', [ProductPricingController::class, 'update'])->name('admin.product-pricing.update');
    Route::get('/admin/product-pricing/export', [ProductPricingController::class, 'downloadTemplate'])->name('admin.product-pricing.export');
    Route::post('/admin/product-pricing/import', [ProductPricingController::class, 'importPricing'])->name('admin.product-pricing.import');

    Route::get('/admin/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/admin/transactions/report', [TransactionController::class, 'report'])->name('transactions.report');
    Route::get('/admin/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/admin/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/admin/transactions/{id}', [TransactionController::class, 'detail'])->name('transactions.detail');
    Route::post('/admin/transactions/{id}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::get('/admin/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/admin/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::post('/admin/transactions/{id}/payment', [TransactionController::class, 'inputPayment'])->name('transactions.payment');
    Route::post('/admin/transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('transactions.status');
    Route::post('/admin/transactions/{id}/deadline', [TransactionController::class, 'updateDeadline'])->name('transactions.deadline');
    
    Route::get('/admin/sales-report', [\App\Http\Controllers\SalesReportController::class, 'index'])->name('sales-report.index');
    Route::get('/admin/sales-report/export', [\App\Http\Controllers\SalesReportController::class, 'export'])->name('sales-report.export');

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
    Route::get('/inventory/overview/{id}', [\App\Http\Controllers\InventoryOverviewController::class, 'detail'])->name('inventory.overview.detail');

    Route::get('/admin/production', [\App\Http\Controllers\ProductionController::class, 'index'])->name('production.index');
    Route::get('/admin/production/create', [\App\Http\Controllers\ProductionController::class, 'create'])->name('production.create');
    Route::get('/admin/production/{id}', [\App\Http\Controllers\ProductionController::class, 'show'])->name('production.show');
    Route::post('/admin/production', [\App\Http\Controllers\ProductionController::class, 'store'])->name('production.store');

    // Cash Book Module
    Route::get('/admin/cash-book', [\App\Http\Controllers\CashBookController::class, 'index'])->name('cash-book.index');
    Route::get('/admin/cash-book/create', [\App\Http\Controllers\CashBookController::class, 'create'])->name('cash-book.create');
    Route::post('/admin/cash-book', [\App\Http\Controllers\CashBookController::class, 'store'])->name('cash-book.store');
    Route::get('/admin/cash-book/{cashBook}/edit', [\App\Http\Controllers\CashBookController::class, 'edit'])->name('cash-book.edit');
    Route::put('/admin/cash-book/{cashBook}', [\App\Http\Controllers\CashBookController::class, 'update'])->name('cash-book.update');
    Route::get('/admin/cash-book/{cashBook}', [\App\Http\Controllers\CashBookController::class, 'show'])->name('cash-book.show');
    Route::delete('/admin/cash-book/{cashBook}', [\App\Http\Controllers\CashBookController::class, 'destroy'])->name('cash-book.destroy');

    // Journal Module
    Route::get('/admin/journal', [\App\Http\Controllers\JournalController::class, 'index'])->name('journal.index');
    Route::get('/admin/journal/export', [\App\Http\Controllers\JournalController::class, 'exportPdf'])->name('journal.export');

    // Profit & Loss Report Module
    Route::get('/admin/reports/profit-loss', \App\Filament\Pages\ProfitLoss::class)->name('reports.profit-loss');
    Route::get('/admin/reports/profit-loss/drilldown', [\App\Http\Controllers\ProfitLossController::class, 'drilldown'])->name('reports.profit-loss.drilldown');
    Route::get('/admin/reports/profit-loss/export-pdf', [\App\Http\Controllers\ProfitLossController::class, 'exportPdf'])->name('reports.profit-loss.export-pdf');
    Route::get('/admin/reports/profit-loss/export-excel', [\App\Http\Controllers\ProfitLossController::class, 'exportExcel'])->name('reports.profit-loss.export-excel');

    // General Ledger Module Exports
    Route::get('/admin/general-ledger/export-excel', [\App\Http\Controllers\GeneralLedgerController::class, 'exportExcel'])->name('admin.general-ledger.export-excel');
    Route::get('/admin/general-ledger/export-pdf', [\App\Http\Controllers\GeneralLedgerController::class, 'exportPdf'])->name('admin.general-ledger.export-pdf');

    // Trial Balance Module
    Route::get('/admin/trial-balance', \App\Filament\Pages\TrialBalance::class)->name('filament.admin.pages.trial-balance');
    Route::get('/admin/trial-balance/export-excel', [\App\Http\Controllers\TrialBalanceController::class, 'exportExcel'])->name('admin.trial-balance.export-excel');
    Route::get('/admin/trial-balance/export-pdf', [\App\Http\Controllers\TrialBalanceController::class, 'exportPdf'])->name('admin.trial-balance.export-pdf');

    // Balance Sheet Module Exports
    Route::get('/admin/balance-sheet/export-excel', [\App\Http\Controllers\BalanceSheetController::class, 'exportExcel'])->name('admin.balance-sheet.export-excel');
    Route::get('/admin/balance-sheet/export-pdf', [\App\Http\Controllers\BalanceSheetController::class, 'exportPdf'])->name('admin.balance-sheet.export-pdf');
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

Route::get('/debug-paths', function () {
    $searchFile = 'dmXZPyFC2hztghK8UKjXhj2W6KnBbNIZqdHTy3UY.png';
    $foundPaths = [];
    
    try {
        $dir = new \RecursiveDirectoryIterator('/home/wwwn9843');
        $iterator = new \RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            if ($file->getFilename() === $searchFile) {
                $foundPaths[] = $file->getPathname();
            }
        }
    } catch (\Exception $e) {
        $foundPaths[] = 'Error: ' . $e->getMessage();
    }

    return [
        'base_path' => base_path(),
        'public_path' => public_path(),
        'public_path_resolved' => realpath(public_path()),
        'found_locations' => $foundPaths,
    ];
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

Route::get('/emergency-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Emergency Migration Berhasil! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class, \Illuminate\View\Middleware\ShareErrorsFromSession::class, \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::get('/run-migration', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration Berhasil! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Terjadi Error saat Migration: ' . $e->getMessage();
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

Route::get('/run-auth-seeder', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'RbacSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'AccountSeeder', '--force' => true]);

        return 'Essential Seeding (Roles, Permissions & Accounts/COA) Berhasil! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Terjadi Error saat Seeding: ' . $e->getMessage();
    }
});

Route::get('/run-superadmin-seeder', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'SuperAdminSeeder', '--force' => true]);
        return 'Super Admin Seeder Berhasil Dijalankan! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Terjadi Error: ' . $e->getMessage();
    }
});
Route::get('/clear-view', function () {
    try {
        Artisan::call('view:clear');
        return 'Blade view cache cleared successfully! <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/clear-config', function () {
    try {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        return 'Config, Route, View, and Cache cleared successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/clear-session', function () {
    try {
        // Clear all files in storage/framework/sessions
        $files = glob(storage_path('framework/sessions/*'));
        foreach ($files as $file) {
            if (is_file($file) && !str_contains($file, '.gitignore')) {
                unlink($file);
            }
        }
        return 'Session files cleared successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::fallback(function () {
    return response()->view('errors.custom', [
        'code' => 404,
        'exception' => new \Exception('We can\'t seem to find the page you\'re looking for.'),
    ], 404);
});
