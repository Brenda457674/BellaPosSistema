<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'tipo_documento'   => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'nombres'          => $this->nombres,
            'apellidos'        => $this->apellidos,
            'nombre_completo'  => trim($this->nombres . ' ' . $this->apellidos),
            'telefono'         => $this->telefono,
            'email'            => $this->email,
            'direccion'        => $this->direccion,
            'estado'           => $this->estado,
        ];
    }
}
