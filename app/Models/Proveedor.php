<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'razon_social',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }
}