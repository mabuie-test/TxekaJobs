<?php

namespace App\Events;

use App\Models\Servico;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServicoCriado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Servico $servico)
    {
    }
}
