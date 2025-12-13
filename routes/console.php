<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CalcularTendenciasCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('txeka:tendencias', CalcularTendenciasCommand::class)
    ->purpose('Recalcular tendências e alimentar recomendações');
