<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Los roles se controlan en routes/api.php
    }

    public function rules(): array
    {
        return [
            'codigo'               => ['required', 'string', 'max:50', 'unique:productos,codigo'],
            'codigo_barras'        => ['nullable', 'string', 'max:100', 'unique:productos,codigo_barras'],
            'nombre'               => ['required', 'string', 'max:150'],
            'categoria_id'         => ['required', 'integer', 'exists:categorias,id'],
            'unidad_medida_id'     => ['required', 'integer', 'exists:unidades_medida,id'],
            'descripcion'          => ['nullable', 'string'],
            'marca'                => ['nullable', 'string', 'max:100'],
            'precio_compra'        => ['required', 'numeric', 'min:0'],
            'precio_venta'         => ['required', 'numeric', 'min:0'],
            'stock_actual'         => ['nullable', 'numeric', 'min:0'],
            'stock_minimo'         => ['nullable', 'numeric', 'min:0'],
            'controla_vencimiento' => ['nullable', 'boolean'],
            'estado'               => ['nullable', Rule::in(['activo', 'inactivo'])],
        ];
    }
}
