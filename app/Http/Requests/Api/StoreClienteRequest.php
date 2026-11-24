<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_documento'   => ['required', Rule::in(['DNI', 'RUC', 'CE', 'OTRO'])],
            'numero_documento' => ['nullable', 'string', 'max:20', 'unique:clientes,numero_documento'],
            'nombres'          => ['required', 'string', 'max:120'],
            'apellidos'        => ['nullable', 'string', 'max:120'],
            'telefono'         => ['nullable', 'string', 'max:25'],
            'email'            => ['nullable', 'email', 'max:150'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'estado'           => ['sometimes', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
