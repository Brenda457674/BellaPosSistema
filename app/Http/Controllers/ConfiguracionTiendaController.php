<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionTiendaController extends Controller
{
    /**
     * Formulario de configuración de la tienda.
     * Solo Administrador.
     */
    public function edit()
    {
        $tienda = DB::table('configuracion_tienda')->first();

        if (!$tienda) {
            DB::table('configuracion_tienda')->insert([
                'nombre'              => 'Bella Flor',
                'razon_social'        => 'Minimarket Bella Flor S.A.C.',
                'ruc'                 => '20601234567',
                'direccion'           => 'Av. Principal 123 - Lima',
                'telefono'            => '987654321',
                'correo'              => 'contacto@bellaflor.pe',
                'moneda'              => 'PEN',
                'simbolo_moneda'      => 'S/',
                'mensaje_comprobante' => '¡Gracias por tu compra en Bella Flor!',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
            $tienda = DB::table('configuracion_tienda')->first();
        }

        return view('configuracion.edit', compact('tienda'));
    }

    /**
     * Guardar cambios de configuración.
     */
    public function update(Request $request)
    {
        $datos = $request->validate([
            'nombre'              => ['required', 'string', 'max:150'],
            'razon_social'        => ['nullable', 'string', 'max:180'],
            'ruc'                 => ['nullable', 'string', 'max:11'],
            'direccion'           => ['nullable', 'string', 'max:255'],
            'telefono'            => ['nullable', 'string', 'max:25'],
            'correo'              => ['nullable', 'email', 'max:150'],
            'moneda'              => ['required', 'string', 'size:3'],
            'simbolo_moneda'      => ['required', 'string', 'max:5'],
            'mensaje_comprobante' => ['nullable', 'string', 'max:255'],
        ]);

        $tienda = DB::table('configuracion_tienda')->first();

        if ($tienda) {
            DB::table('configuracion_tienda')->where('id', $tienda->id)->update($datos + [
                'updated_at' => now(),
            ]);
        } else {
            DB::table('configuracion_tienda')->insert($datos + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('configuracion.edit')
            ->with('ok', 'Datos de la tienda actualizados correctamente.');
    }
}
