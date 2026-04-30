<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // Let Dashboard pass
        if ($path === 'admin' || str_starts_with($path, 'admin/login') || str_starts_with($path, 'livewire')) {
            return $next($request);
        }

        // We only want to protect mapped menu paths. Since filament appends things like /create, /edit, 
        // we'll find if the path starts with any of our mapped routes.
        $mappedRoutes = [
            'admin/landing-page',
            'admin/appearance',
            'admin/products',
            'cek-stok/product',
            'admin/sales-report',
            'admin/transactions/report',
            'admin/transactions',
            'admin/pre-orders',
            'admin/items',
            '/inventory/overview',
            '/coming-soon',
            'admin/product-types',
            'admin/size-options',
            'admin/units',
            'admin/accounts',
            'admin/role-permissions',
        ];

        foreach ($mappedRoutes as $route) {
            $checkRoute = ltrim($route, '/');
            if (str_starts_with($path, $checkRoute)) {
                if (!canAccessMenu($route)) {
                    abort(403, 'Anda tidak memiliki akses ke halaman ini.');
                }
                break;
            }
        }

        return $next($request);
    }
}
