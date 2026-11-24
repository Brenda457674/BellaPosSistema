<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('producto');

        // El stock NO se cambia aquí (igual que en el sistema web).
        return [
            'codigo'               => ['required', 'string', 'max:50', Rule::unique('productos', 'codigo')->ignore($id)],
            'codigo_barras'        => ['nullable', 'string', 'max:100', Rule::unique('productos', 'codigo_barras')->ignore($id)],
            'nombre'               => ['required', 'string', 'max:150'],
            'categoria_id'         => ['required', 'integer', 'exists:categorias,id'],
            'unidad_medida_id'     => ['required', 'integer', 'exists:unidades_medida,id'],
            'descripcion'          => ['nullable', 'string'],
            'marca'                => ['nullable', 'string', 'max:100'],
            'precio_compra'        => ['required', 'numeric', 'min:0'],
            'precio_venta'         => ['required', 'numeric', 'min:0'],
            'stock_minimo'         => ['nullable', 'numeric', 'min:0'],
            'controla_vencimiento' => ['nullable', 'boolean'],
            'estado'               => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
