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
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()->label('Page Editor')->icon('heroicon-o-document-duplicate'),
                \Filament\Navigation\NavigationGroup::make()->label('Catalog')->icon('heroicon-o-shopping-bag'),
                \Filament\Navigation\NavigationGroup::make()->label('Sales')->icon('heroicon-o-banknotes'),
                \Filament\Navigation\NavigationGroup::make()->label('Inventory')->icon('heroicon-o-inbox-stack'),
                \Filament\Navigation\NavigationGroup::make()->label('Accounting')->icon('heroicon-o-calculator'),
                \Filament\Navigation\NavigationGroup::make()->label('Master Data')->icon('heroicon-o-folder-open'),
                \Filament\Navigation\NavigationGroup::make()->label('User Management')->icon('heroicon-o-users'),
            ])
            ->navigationItems([
                // Catalog custom link
                \Filament\Navigation\NavigationItem::make('Products Inventory')
                    ->group('Catalog')
                    ->url(fn(): string => route('cek-stok.product'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->visible(fn() => canAccessMenu('cek-stok/product'))
                    ->sort(2),

                \Filament\Navigation\NavigationItem::make('Product Pricing')
                    ->group('Catalog')
                    ->url(fn(): string => route('admin.product-pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->isActiveWhen(fn() => request()->routeIs('admin.product-pricing'))
                    ->visible(fn() => canAccessMenu('admin/product-pricing'))
                    ->sort(3),

                // Sales links
                \Filament\Navigation\NavigationItem::make('Orders')
                    ->group('Sales')
                    ->url(fn(): string => route('transactions.index'))
                    ->icon('heroicon-o-shopping-cart')
                    ->isActiveWhen(fn() => request()->routeIs('transactions.*') && !request()->routeIs('transactions.report'))
                    ->visible(fn() => canAccessMenu('admin/transactions'))
                    ->sort(1),
                \Filament\Navigation\NavigationItem::make('Pre Order / Quotation')
                    ->group('Sales')
                    ->url(fn(): string => route('pre-orders.index'))
                    ->icon('heroicon-o-document-text')
                    ->isActiveWhen(fn() => request()->routeIs('pre-orders.*'))
                    ->visible(fn() => canAccessMenu('admin/pre-orders'))
                    ->sort(2),
                \Filament\Navigation\NavigationItem::make('Sales Dashboard')
                    ->group('Sales')
                    ->url(fn(): string => route('transactions.report'))
                    ->icon('heroicon-o-chart-pie')
                    ->isActiveWhen(fn() => request()->routeIs('transactions.report'))
                    ->visible(fn() => canAccessMenu('admin/transactions/report'))
                    ->sort(3),
                \Filament\Navigation\NavigationItem::make('Sales Report')
                    ->group('Sales')
                    ->url(fn(): string => route('sales-report.index'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->isActiveWhen(fn() => request()->routeIs('sales-report.*'))
                    ->visible(fn() => canAccessMenu('admin/sales-report'))
                    ->sort(4),

                // Inventory Links (Coming soon except Overview)
                \Filament\Navigation\NavigationItem::make('Inventory Overview')
                    ->group('Inventory')
                    ->url(fn(): string => route('inventory.overview'))
                    ->icon('heroicon-o-presentation-chart-line')
                    ->isActiveWhen(fn() => request()->routeIs('inventory.overview'))
                    ->visible(fn() => canAccessMenu('inventory/overview'))
                    ->sort(2),
                \Filament\Navigation\NavigationItem::make('Production')
                    ->group('Inventory')
                    ->url(fn(): string => route('production.index'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->isActiveWhen(fn() => request()->routeIs('production.*'))
                    ->visible(fn() => canAccessMenu('admin/production'))
                    ->sort(6),
                
                // Accounting Links
                \Filament\Navigation\NavigationItem::make('C.O.A (Chart Of Accounts)')
                    ->group('Accounting')
                    ->url(fn(): string => route('filament.admin.resources.coa.index'))
                    ->icon('heroicon-o-list-bullet')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.coa.*'))
                    ->visible(fn() => canAccessMenu('admin/coa'))
                    ->sort(1),
                \Filament\Navigation\NavigationItem::make('Opening Balance')
                    ->group('Accounting')
                    ->url(fn(): string => route('filament.admin.resources.opening-balance.index'))
                    ->icon('heroicon-o-scale')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.opening-balance.*'))
                    ->visible(fn() => canAccessMenu('admin/opening-balance'))
                    ->sort(2),
                \Filament\Navigation\NavigationItem::make('Bank Transfers')
                    ->group('Accounting')
                    ->url(fn(): string => route('filament.admin.resources.bank-transfers.index'))
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.bank-transfers.*'))
                    ->visible(fn() => canAccessMenu('admin/bank-transfers'))
                    ->sort(3),
                \Filament\Navigation\NavigationItem::make('Cash Book')
                    ->group('Accounting')
                    ->url(fn(): string => route('cash-book.index'))
                    ->icon('heroicon-o-currency-dollar')
                    ->isActiveWhen(fn() => request()->routeIs('cash-book.*'))
                    ->visible(fn() => canAccessMenu('admin/cash-book'))
                    ->sort(4),
                \Filament\Navigation\NavigationItem::make('Transaction Template')
                    ->group('Accounting')
                    ->url(fn(): string => route('coming-soon'))
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->visible(fn() => canAccessMenu('/coming-soon'))
                    ->sort(5),
                \Filament\Navigation\NavigationItem::make('Journal')
                    ->group('Accounting')
                    ->url(fn(): string => route('journal.index'))
                    ->icon('heroicon-o-book-open')
                    ->isActiveWhen(fn() => request()->routeIs('journal.*'))
                    ->visible(fn() => canAccessMenu('admin/journal'))
                    ->sort(6),
                \Filament\Navigation\NavigationItem::make('General Ledger')
                    ->group('Accounting')
                    ->url(fn(): string => route('coming-soon'))
                    ->icon('heroicon-o-table-cells')
                    ->visible(fn() => canAccessMenu('/coming-soon'))
                    ->sort(7),





                // Page Editor links
                \Filament\Navigation\NavigationItem::make('Appearance')
                    ->group('Page Editor')
                    ->url(fn(): string => route('admin.appearance.index'))
                    ->icon('heroicon-o-cog')
                    ->isActiveWhen(fn() => request()->routeIs('admin.appearance.*'))
                    ->visible(fn() => canAccessMenu('admin/appearance'))
                    ->sort(2),
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
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
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLanguage::class,
                \App\Http\Middleware\CheckMenuPermission::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
