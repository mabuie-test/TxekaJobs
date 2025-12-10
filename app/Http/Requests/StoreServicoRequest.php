<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'zona_id' => ['nullable', 'integer', 'exists:zonas,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string', 'min:20'],
            'cidade' => ['required', 'string', 'max:120'],
            'bairro_texto' => ['required', 'string', 'max:120'],
            'referencia_localizacao_texto' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'orcamento_estimado_min' => ['nullable', 'numeric', 'gte:0'],
            'orcamento_estimado_max' => ['nullable', 'numeric', 'gte:orcamento_estimado_min'],
            'urgencia' => ['required', 'in:agora,hoje,esta_semana'],
            'origem' => ['nullable', 'in:web,pwa'],
        ];
    }
}
