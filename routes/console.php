<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('txeka:tendencias', function () {
    return app(\App\Console\Commands\CalcularTendenciasCommand::class)->handle();
})->purpose('Recalcular tendências e alimentar recomendações');
