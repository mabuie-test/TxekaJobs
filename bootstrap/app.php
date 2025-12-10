<?php

use App\Console\Commands\RecalcularEstatisticasPrestadores;
use App\Providers\EventServiceProvider;
use App\Providers\PaymentServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        PaymentServiceProvider::class,
        EventServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        RecalcularEstatisticasPrestadores::class,
    ])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('txeka:recalcular-estatisticas')->dailyAt('02:00');
    })
    ->withMiddleware(function ($middleware) {
        // placeholder for middleware registration
    })
    ->create();
