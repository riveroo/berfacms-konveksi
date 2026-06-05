<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Enums\ThemeMode;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $brandLogoHtml = fn() => new \Illuminate\Support\HtmlString('
            <div class="flex flex-col items-end">
                <img src="' . e(
                    (function() {
                        try {
                            $appearance = \App\Models\AppearanceSetting::first();
                            if ($appearance && $appearance->header_logo) {
                                return asset('storage/' . $appearance->header_logo);
                            }
                        } catch (\Throwable $e) {}
                        return asset('images/logo.png');
                    })()
                ) . '" alt="Berfa CMS" style="height: 3rem;" class="object-contain" />
                <span style="font-size: 10px; color: #9ca3af; font-weight: 500; letter-spacing: 0.05em; margin-top: -2px; display: block; text-align: right; width: 100%;">by berfacms</span>
            </div>
        ');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15%')
            ->brandName('Berfa CMS')
            ->brandLogo($brandLogoHtml)
            ->brandLogoHeight('4rem')
            ->favicon(asset('images/favicon.png'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.Page Editor'))->icon('heroicon-o-document-duplicate'),
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.Catalog'))->icon('heroicon-o-shopping-bag'),
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.Sales'))->icon('heroicon-o-banknotes'),
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.Inventory'))->icon('heroicon-o-inbox-stack'),
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.Accounting'))->icon('heroicon-o-calculator'),
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.Master Data'))->icon('heroicon-o-folder-open'),
                \Filament\Navigation\NavigationGroup::make()->label(fn() => __('sidebar.User Management'))->icon('heroicon-o-users'),
            ])
            ->navigationItems([
                // Catalog custom link
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Products Inventory'))
                    ->group(fn() => __('sidebar.Catalog'))
                    ->url(fn(): string => route('cek-stok.product'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->isActiveWhen(fn() => request()->routeIs('cek-stok.product'))
                    ->visible(fn() => canAccessMenu('cek-stok/product'))
                    ->sort(2),

                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Product Pricing'))
                    ->group(fn() => __('sidebar.Catalog'))
                    ->url(fn(): string => route('admin.product-pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->isActiveWhen(fn() => request()->routeIs('admin.product-pricing'))
                    ->visible(fn() => canAccessMenu('admin/product-pricing'))
                    ->sort(3),

                // Sales links
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Orders'))
                    ->group(fn() => __('sidebar.Sales'))
                    ->url(fn(): string => route('transactions.index'))
                    ->icon('heroicon-o-shopping-cart')
                    ->isActiveWhen(fn() => request()->routeIs('transactions.*') && !request()->routeIs('transactions.report'))
                    ->visible(fn() => canAccessMenu('admin/transactions'))
                    ->sort(1),
                 \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Pre Order / Quotation'))
                    ->group(fn() => __('sidebar.Sales'))
                    ->url(fn(): string => route('pre-orders.index'))
                    ->icon('heroicon-o-document-text')
                    ->isActiveWhen(fn() => request()->routeIs('pre-orders.*'))
                    ->visible(fn() => canAccessMenu('admin/pre-orders'))
                    ->sort(2),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Accounts Receivable'))
                    ->group(fn() => __('sidebar.Sales'))
                    ->url(fn(): string => route('filament.admin.pages.accounts-receivable'))
                    ->icon('heroicon-o-credit-card')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.accounts-receivable'))
                    ->visible(fn() => canAccessMenu('admin/accounts-receivable'))
                    ->sort(3),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Sales Dashboard'))
                    ->group(fn() => __('sidebar.Sales'))
                    ->url(fn(): string => route('transactions.report'))
                    ->icon('heroicon-o-chart-pie')
                    ->isActiveWhen(fn() => request()->routeIs('transactions.report'))
                    ->visible(fn() => canAccessMenu('admin/transactions/report'))
                    ->sort(4),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Sales Report'))
                    ->group(fn() => __('sidebar.Sales'))
                    ->url(fn(): string => route('sales-report.index'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->isActiveWhen(fn() => request()->routeIs('sales-report.*'))
                    ->visible(fn() => canAccessMenu('admin/sales-report'))
                    ->sort(5),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Customers'))
                    ->group(fn() => __('sidebar.Sales'))
                    ->url(fn(): string => route('filament.admin.pages.customers'))
                    ->icon('heroicon-o-users')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.customers'))
                    ->visible(fn() => canAccessMenu('admin/customers'))
                    ->sort(6),

                // Inventory Links (Coming soon except Overview)
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Inventory Overview'))
                    ->group(fn() => __('sidebar.Inventory'))
                    ->url(fn(): string => route('inventory.overview'))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->isActiveWhen(fn() => request()->routeIs('inventory.overview'))
                    ->visible(fn() => canAccessMenu('inventory/overview'))
                    ->sort(2),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Production'))
                    ->group(fn() => __('sidebar.Inventory'))
                    ->url(fn(): string => route('production.index'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->isActiveWhen(fn() => request()->routeIs('production.*'))
                    ->visible(fn() => canAccessMenu('admin/production'))
                    ->sort(6),
                
                // Accounting Links
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.C.O.A (Chart Of Accounts)'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('filament.admin.resources.coa.index'))
                    ->icon('heroicon-o-list-bullet')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.coa.*'))
                    ->visible(fn() => canAccessMenu('admin/coa'))
                    ->sort(1),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Opening Balance'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('filament.admin.resources.opening-balance.index'))
                    ->icon('heroicon-o-scale')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.opening-balance.*'))
                    ->visible(fn() => canAccessMenu('admin/opening-balance'))
                    ->sort(2),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Bank Transfers'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('filament.admin.resources.bank-transfers.index'))
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.bank-transfers.*'))
                    ->visible(fn() => canAccessMenu('admin/bank-transfers'))
                    ->sort(3),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Cash Book'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('cash-book.index'))
                    ->icon('heroicon-o-currency-dollar')
                    ->isActiveWhen(fn() => request()->routeIs('cash-book.*'))
                    ->visible(fn() => canAccessMenu('admin/cash-book'))
                    ->sort(4),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Journal'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('journal.index'))
                    ->icon('heroicon-o-book-open')
                    ->isActiveWhen(fn() => request()->routeIs('journal.*'))
                    ->visible(fn() => canAccessMenu('admin/journal'))
                    ->sort(6),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.General Ledger'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('filament.admin.pages.general-ledger'))
                    ->icon('heroicon-o-table-cells')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.general-ledger'))
                    ->visible(fn() => canAccessMenu('admin/general-ledger'))
                    ->sort(7),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Trial Balance'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('filament.admin.pages.trial-balance'))
                    ->icon('heroicon-o-scale')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.trial-balance'))
                    ->visible(fn() => canAccessMenu('admin/trial-balance'))
                    ->sort(8),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Profit & Loss'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('reports.profit-loss'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->isActiveWhen(fn() => request()->routeIs('reports.profit-loss'))
                    ->visible(fn() => canAccessMenu('admin/reports/profit-loss'))
                    ->sort(9),
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Balance Sheet'))
                    ->group(fn() => __('sidebar.Accounting'))
                    ->url(fn(): string => route('filament.admin.pages.balance-sheet'))
                    ->icon('heroicon-o-book-open')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.balance-sheet'))
                    ->visible(fn() => canAccessMenu('admin/balance-sheet'))
                    ->sort(10),

                // Page Editor links
                \Filament\Navigation\NavigationItem::make(fn() => __('sidebar.Appearance'))
                    ->group(fn() => __('sidebar.Page Editor'))
                    ->url(fn(): string => route('admin.appearance.index'))
                    ->icon('heroicon-o-cog')
                    ->isActiveWhen(fn() => request()->routeIs('admin.appearance.*'))
                    ->visible(fn() => canAccessMenu('admin/appearance'))
                    ->sort(2),
            ])
            ->userMenuItems([
                \Filament\Navigation\UserMenuItem::make()
                    ->label('Change Password')
                    ->url(fn (): string => \App\Filament\Pages\ChangePassword::getUrl())
                    ->icon('heroicon-o-key'),
            ])
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => Blade::render('<div class="flex items-center gap-2 mr-4">
                    <a href="{{ route(\'switch.locale\', \'en\') }}" class="text-sm border px-2 py-1 rounded {{ app()->getLocale() == \'en\' ? \'bg-primary-500 text-white font-bold border-primary-500\' : \'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 border-gray-300 dark:border-gray-700\' }}">EN</a>
                    <a href="{{ route(\'switch.locale\', \'id\') }}" class="text-sm border px-2 py-1 rounded {{ app()->getLocale() == \'id\' ? \'bg-primary-500 text-white font-bold border-primary-500\' : \'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 border-gray-300 dark:border-gray-700\' }}">ID</a>
                </div>')
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                \App\Http\Middleware\SetLanguage::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\CheckMenuPermission::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
