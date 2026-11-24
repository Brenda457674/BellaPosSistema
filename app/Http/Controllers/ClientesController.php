<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ClientesController extends Controller
{
    /**
     * Reglas de validación para clientes.
     */
    private function reglas(?int $id = null): array
    {
        return [
            'tipo_documento'   => ['required', Rule::in(['DNI', 'RUC', 'CE', 'OTRO'])],
            'numero_documento' => ['nullable', 'string', 'max:20', Rule::unique('clientes', 'numero_documento')->ignore($id)],
            'nombres'          => ['required', 'string', 'max:120'],
            'apellidos'        => ['nullable', 'string', 'max:120'],
            'telefono'         => ['nullable', 'string', 'max:25'],
            'email'            => ['nullable', 'email', 'max:150'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'estado'           => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    /**
     * Buscar un cliente por su ID o abortar con 404.
     */
    private function buscar(int $id)
    {
        return DB::table('clientes')->where('id', $id)->first() ?? abort(404, 'Cliente no encontrado.');
    }

    /**
     * Listado con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $clientes = DB::table('clientes')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%")
                      ->orWhere('numero_documento', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'q'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Guardar nuevo cliente.
     */
    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas());

        DB::table('clientes')->insert($datos + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('clientes.index')->with('ok', 'Cliente registrado exitosamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(int $id)
    {
        $cliente = $this->buscar($id);

        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar cliente existente.
     */
    public function update(Request $request, int $id)
    {
        $this->buscar($id);
        $datos = $request->validate($this->reglas($id));

        DB::table('clientes')->where('id', $id)->update($datos + [
            'updated_at' => now(),
        ]);

        return redirect()->route('clientes.index')->with('ok', 'Cliente actualizado exitosamente.');
    }

    /**
     * Eliminar o desactivar cliente.
     * Solo Administrador. Si tiene ventas, se desactiva para mantener la integridad de los comprobantes.
     */
    public function destroy(int $id)
    {
        if (Auth::user()->rol?->nombre !== 'Administrador') {
            abort(403, 'Solo el Administrador puede eliminar o desactivar clientes.');
        }

        $this->buscar($id);

        $tieneVentas = DB::table('ventas')->where('cliente_id', $id)->exists();

        if ($tieneVentas) {
            DB::table('clientes')->where('id', $id)->update([
                'estado' => 'inactivo',
                'updated_at' => now(),
            ]);

            return redirect()->route('clientes.index')
                ->with('ok', 'El cliente tiene ventas registradas, por lo que fue desactivado para proteger el historial.');
        }

        DB::table('clientes')->where('id', $id)->delete();

        return redirect()->route('clientes.index')->with('ok', 'Cliente eliminado correctamente.');
    }
}
