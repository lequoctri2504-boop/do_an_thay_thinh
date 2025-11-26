<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global middleware
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Middleware nhóm "web" – BẮT BUỘC CÓ ĐỦ ĐỂ SESSION + SOCIALITE CHẠY
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,                 // ← QUAN TRỌNG NHẤT
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,      // ← ĐÃ DÙNG Illuminate
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware
     */
    protected $routeMiddleware = [
        'auth'     => \Illuminate\Auth\Middleware\Authenticate::class,             // ← Illuminate
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'    => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,  // ← Illuminate
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'signed'   => \Illuminate\Routing\Middleware\ValidateSignature::class,
    ];
}