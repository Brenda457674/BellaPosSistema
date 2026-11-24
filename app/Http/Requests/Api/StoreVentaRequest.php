<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'          => ['nullable', 'integer', 'exists:clientes,id'],
            'metodo_pago_id'      => ['required', 'integer', 'exists:metodos_pago,id'],
            'referencia_pago'     => ['nullable', 'string', 'max:100'],
            'monto_recibido'      => ['nullable', 'numeric', 'min:0'],
            'observaciones'       => ['nullable', 'string', 'max:255'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad'    => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'El carrito está vacío. Agrega al menos un producto.',
            'items.min'      => 'El carrito está vacío. Agrega al menos un producto.',
            'items.*.producto_id.exists' => 'Uno de los productos no existe.',
        ];
    }
}
