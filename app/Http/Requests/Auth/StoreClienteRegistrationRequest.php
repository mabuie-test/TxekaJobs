<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'morada_principal' => ['nullable', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:120'],
            'bairro_principal' => ['nullable', 'string', 'max:120'],
            'referencia_localizacao_texto' => ['nullable', 'string', 'max:500'],
        ];
    }
}
