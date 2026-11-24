<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'nombres'         => $this->nombres,
            'apellidos'       => $this->apellidos,
            'nombre_completo' => $this->name,
            'email'           => $this->email,
            'telefono'        => $this->telefono,
            'estado'          => $this->estado,
            'rol'             => $this->rol?->nombre,
            'correo_verificado' => $this->hasVerifiedEmail(),
        ];
    }
}
