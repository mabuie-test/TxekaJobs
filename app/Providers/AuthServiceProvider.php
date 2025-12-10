<?php

namespace App\Providers;

use App\Models\Servico;
use App\Policies\ServicoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Servico::class => ServicoPolicy::class,
    ];
}
