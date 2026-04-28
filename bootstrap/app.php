<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Ignore API or JSON requests
            if ($request->wantsJson() || $request->is('api/*')) {
                return null;
            }

            // Only override rendering if it's an HTTP exception or server error, skip auth/validation redirects
            if ($e instanceof \Illuminate\Validation\ValidationException || $e instanceof \Illuminate\Auth\AuthenticationException) {
                return null;
            }

            $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            // Optional: You can wrap this in if (app()->environment('production')) if you only want it in production.
            // But per request, local shows trace, so we can always show it.
            return response()->view('errors.custom', [
                'exception' => $e,
                'code' => $code,
            ], $code === 0 ? 500 : $code);
        });
    })->create();
