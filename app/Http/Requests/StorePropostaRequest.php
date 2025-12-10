<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropostaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valor_proposto' => ['required', 'numeric', 'min:0'],
            'mensagem' => ['required', 'string', 'min:10'],
            'tempo_estimado_execucao' => ['nullable', 'string', 'max:100'],
        ];
    }
}
