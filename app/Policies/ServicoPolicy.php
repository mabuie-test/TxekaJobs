<?php

namespace App\Policies;

use App\Models\Servico;
use App\Models\User;

class ServicoPolicy
{
    public function view(User $user, Servico $servico): bool
    {
        if ($user->tipo_perfil === 'admin') {
            return true;
        }

        if ($user->tipo_perfil === 'cliente') {
            return $user->cliente && $user->cliente->id === $servico->cliente_id;
        }

        if ($user->tipo_perfil === 'prestador') {
            return $servico->prestador_id && $servico->prestador_id === optional($user->prestador)->id;
        }

        return false;
    }

    public function propor(User $user, Servico $servico): bool
    {
        if ($user->tipo_perfil !== 'prestador') {
            return false;
        }

        if ($servico->cliente && $servico->cliente->user_id === $user->id) {
            return false; // não permitir propor ao próprio pedido
        }

        return in_array($servico->estado, ['aberto', 'em_propostas'], true);
    }
}
