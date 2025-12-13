<?php

namespace App\Listeners;

use App\Events\ServicoCriado;
use App\Jobs\MatchPrestadoresJob;

class AgendarMatchingListener
{
    public function handle(ServicoCriado $event): void
    {
        MatchPrestadoresJob::dispatch($event->servico);
    }
}
