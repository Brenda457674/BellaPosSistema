<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionTienda extends Model
{
    use HasFactory;

    protected $table = 'configuracion_tienda';

    protected $fillable = [
        'nombre',
        'razon_social',
        'ruc',
        'direccion',
        'telefono',
        'correo',
        'logo',
        'moneda',
        'simbolo_moneda',
        'mensaje_comprobante',
    ];
}