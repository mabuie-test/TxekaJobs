<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RestoreBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->tipo_perfil === 'admin';
    }

    public function rules(): array
    {
        return [
            'backup_file' => ['required', 'file', 'mimes:sql,txt', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'backup_file.max' => 'O ficheiro de backup não pode exceder 50MB.',
        ];
    }
}
