<?php

namespace App\Providers;

use App\Events\ServicoConcluido;
use App\Events\ServicoCriado;
use App\Listeners\ActualizarEstatisticasPrestadorListener;
use App\Listeners\AgendarMatchingListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ServicoCriado::class => [
            AgendarMatchingListener::class,
        ],
        ServicoConcluido::class => [
            ActualizarEstatisticasPrestadorListener::class,
        ],
    ];
}
