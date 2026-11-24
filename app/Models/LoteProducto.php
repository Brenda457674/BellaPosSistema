<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoteProducto extends Model
{
    use HasFactory;

    protected $table = 'lotes_productos';

    protected $fillable = [
        'producto_id',
        'detalle_compra_id',
        'codigo_lote',
        'cantidad_inicial',
        'cantidad_disponible',
        'fecha_ingreso',
        'fecha_vencimiento',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'cantidad_inicial' => 'decimal:3',
        'cantidad_disponible' => 'decimal:3',
        'fecha_ingreso' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class, 'detalle_compra_id');
    }

    public function estaVencido()
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    public function estaPorVencer($dias = 30)
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isBetween(now(), now()->addDays($dias));
    }
}