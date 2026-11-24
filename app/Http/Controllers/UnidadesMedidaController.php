<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UnidadesMedidaController extends Controller
{
    /**
     * Listado de unidades de medida.
     * Roles: Administrador y Almacenero.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $rol = auth()->user()->rol?->nombre ?? '';
        $puedeEliminar = ($rol === 'Administrador');

        $unidades = DB::table('unidades_medida')
            ->select('unidades_medida.*', DB::raw('(SELECT COUNT(*) FROM productos WHERE productos.unidad_medida_id = unidades_medida.id) as productos_count'))
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                      ->orWhere('abreviatura', 'like', "%{$q}%");
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('unidades.index', compact('unidades', 'q', 'puedeEliminar'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'            => ['required', 'string', 'max:50', 'unique:unidades_medida,nombre'],
            'abreviatura'       => ['required', 'string', 'max:15', 'unique:unidades_medida,abreviatura'],
            'permite_decimales' => ['required', 'boolean'],
        ]);

        DB::table('unidades_medida')->insert($datos + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('unidades.index')->with('ok', 'Unidad de medida registrada.');
    }

    public function edit(int $id)
    {
        $unidad = DB::table('unidades_medida')->where('id', $id)->first() ?? abort(404);

        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, int $id)
    {
        $unidad = DB::table('unidades_medida')->where('id', $id)->first() ?? abort(404);

        $datos = $request->validate([
            'nombre'            => ['required', 'string', 'max:50', Rule::unique('unidades_medida', 'nombre')->ignore($id)],
            'abreviatura'       => ['required', 'string', 'max:15', Rule::unique('unidades_medida', 'abreviatura')->ignore($id)],
            'permite_decimales' => ['required', 'boolean'],
        ]);

        DB::table('unidades_medida')->where('id', $id)->update($datos + [
            'updated_at' => now(),
        ]);

        return redirect()->route('unidades.index')->with('ok', 'Unidad de medida actualizada.');
    }

    /**
     * Eliminar unidad. Solo Administrador.
     * Regla: No se puede eliminar una unidad si tiene productos asociados.
     */
    public function destroy(int $id)
    {
        if (auth()->user()->rol?->nombre !== 'Administrador') {
            abort(403, 'Solo el Administrador puede eliminar unidades de medida.');
        }

        $unidad = DB::table('unidades_medida')->where('id', $id)->first() ?? abort(404);

        $tieneProductos = DB::table('productos')->where('unidad_medida_id', $id)->exists();

        if ($tieneProductos) {
            return redirect()->route('unidades.index')
                ->with('error', "No se puede eliminar la unidad '{$unidad->nombre}' porque está asignada a productos.");
        }

        DB::table('unidades_medida')->where('id', $id)->delete();

        return redirect()->route('unidades.index')->with('ok', 'Unidad de medida eliminada.');
    }
}
