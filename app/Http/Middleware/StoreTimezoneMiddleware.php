<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreTimezoneMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-Device-Timezone')) {
            Session::put('device_timezone', $request->header('X-Device-Timezone'));
        } elseif ($request->has('device_timezone')) {
            Session::put('device_timezone', $request->input('device_timezone'));
        } elseif (isset($_COOKIE['device_timezone'])) {
            Session::put('device_timezone', $_COOKIE['device_timezone']);
        }
        return $next($request);
    }
}
