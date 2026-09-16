<?php

use App\Http\Middleware\HandleAppearance;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        $middleware->validateCsrfTokens(except: [
            'habblet/ajax/*',
            'habblet/habbosearchcontent',
            'habblet/mytagslist',
            'habblet/report_user',
            'grouppurchase/*',
            'myhabbo/*',
            'groups/actions/*',
            'minimail/*',
            'components/*',
            'friendmanagement/*',
            'habboclub/*',
            'discussions/*',
            'mod/*',
            'trax/*',
            'profile/wardrobeStore',
            'credits/habboclub',
        ]);

        $middleware->alias([
            'hotel.auth' => \App\Http\Middleware\HotelAuthenticate::class,
            'hotel.guest' => \App\Http\Middleware\RedirectIfHotelAuthenticated::class,
            'hotel.optional' => \App\Http\Middleware\OptionalHotelUser::class,
            'hotel.staff' => \App\Http\Middleware\HotelStaff::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            \App\Http\Middleware\HotelMaintenance::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return response()->view('hotel.error', [
                'pageId' => 'error',
                'pageName' => 'Page not found',
                'bodyId' => 'home',
                'cat' => 'community',
                'hotelUser' => $request->attributes->get('hotelUser'),
            ], 404);
        });
    })->create();
