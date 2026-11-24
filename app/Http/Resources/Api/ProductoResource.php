<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // El Vendedor no ve el costo de compra
        $verCosto = $request->user()?->rol?->nombre !== 'Vendedor';

        return [
            'id'                   => $this->id,
            'codigo'               => $this->codigo,
            'codigo_barras'        => $this->codigo_barras,
            'nombre'               => $this->nombre,
            'descripcion'          => $this->descripcion,
            'marca'                => $this->marca,
            'categoria'            => [
                'id'     => $this->categoria_id,
                'nombre' => $this->categoria_nombre,
            ],
            'unidad'               => [
                'id'          => $this->unidad_medida_id,
                'nombre'      => $this->unidad_nombre,
                'abreviatura' => $this->unidad_abreviatura,
            ],
            'precio_compra'        => $this->when($verCosto, (float) $this->precio_compra),
            'precio_venta'         => (float) $this->precio_venta,
            'stock_actual'         => (float) $this->stock_actual,
            'stock_minimo'         => (float) $this->stock_minimo,
            'stock_bajo'           => (float) $this->stock_actual <= (float) $this->stock_minimo,
            'controla_vencimiento' => (bool) $this->controla_vencimiento,
            'estado'               => $this->estado,
        ];
    }
}
