<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'unidad_medida_id',
        'codigo',
        'codigo_barras',
        'nombre',
        'descripcion',
        'marca',
        'precio_compra',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'foto',
        'controla_vencimiento',
        'estado',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'decimal:3',
        'stock_minimo' => 'decimal:3',
        'controla_vencimiento' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function lotes()
    {
        return $this->hasMany(LoteProducto::class, 'producto_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'producto_id');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    public function tieneStockBajo()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}