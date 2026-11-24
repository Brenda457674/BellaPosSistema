<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $r = $this->resource;

        return [
            'id'            => $r->id,
            'comprobante'   => "{$r->serie}-{$r->numero}",
            'serie'         => $r->serie,
            'numero'        => $r->numero,
            'fecha_venta'   => $r->fecha_venta,
            'estado'        => $r->estado,
            'cliente'       => [
                'id'     => $r->cliente_id,
                'nombre' => trim($r->cliente_nombre),
            ],
            'vendedor'      => [
                'id'     => $r->usuario_id,
                'nombre' => trim($r->vendedor_nombre),
            ],
            'subtotal'      => (float) $r->subtotal,
            'impuesto'      => (float) $r->impuesto,
            'descuento'     => (float) $r->descuento,
            'total'         => (float) $r->total,
            'observaciones' => $r->observaciones,

            // Solo aparecen cuando se pide una venta puntual (show / store)
            'pago' => $this->when(isset($r->pago), function () use ($r) {
                return [
                    'metodo'     => $r->pago->metodo_nombre,
                    'monto'      => (float) $r->pago->monto,
                    'referencia' => $r->pago->referencia,
                    'estado'     => $r->pago->estado,
                ];
            }),
            'detalles' => $this->when(isset($r->detalles), function () use ($r) {
                return $r->detalles->map(fn ($d) => [
                    'producto_id'     => $d->producto_id,
                    'codigo'          => $d->producto_codigo,
                    'producto'        => $d->producto_nombre,
                    'unidad'          => $d->unidad,
                    'cantidad'        => (float) $d->cantidad,
                    'precio_unitario' => (float) $d->precio_unitario,
                    'subtotal'        => (float) $d->subtotal,
                ])->values();
            }),
        ];
    }
}
