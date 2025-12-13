<?php

use App\Console\Commands\RecalcularEstatisticasPrestadores;
use App\Console\Commands\BackupDatabaseCommand;
use App\Console\Commands\CalcularTendenciasCommand;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\PaymentServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        PaymentServiceProvider::class,
        AuthServiceProvider::class,
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        RecalcularEstatisticasPrestadores::class,
        BackupDatabaseCommand::class,
        CalcularTendenciasCommand::class,
    ])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('txeka:recalcular-estatisticas')->dailyAt('02:00');
        $schedule->command('txeka:backup-diario')->dailyAt('03:00');
    })
    ->withMiddleware(function ($middleware) {
        $middleware->append([
            App\Http\Middleware\TrustProxies::class,
            App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            App\Http\Middleware\TrimStrings::class,
            App\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->web([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            App\Http\Middleware\VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->api([
            ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => App\Http\Middleware\Authenticate::class,
            'guest' => App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => App\Http\Middleware\EnsureEmailIsVerified::class,
            'throttle' => ThrottleRequests::class,
            'signed' => ValidateSignature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash(['password', 'password_confirmation']);
    })
    ->create();
