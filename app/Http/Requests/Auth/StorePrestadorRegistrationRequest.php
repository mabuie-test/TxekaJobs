<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StorePrestadorRegistrationRequest extends FormRequest
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
            'bio' => ['nullable', 'string', 'max:500'],
            'tipo_documento' => ['required', 'in:bilhete_identidade,passaporte,dire,carta_conducao'],
            'numero_documento' => ['required', 'string', 'max:120'],
            'tipo_carteira' => ['required', 'in:mpesa,mkesh,emola,outro'],
            'numero_carteira' => ['required', 'string', 'max:50'],
            'cidade' => ['required', 'string', 'max:120'],
            'bairro_principal' => ['nullable', 'string', 'max:120'],
            'aceita_servicos_urgentes' => ['sometimes', 'boolean'],
        ];
    }
}
