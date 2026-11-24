<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoriasController extends Controller
{
    /**
     * Listar categorías.
     */
    public function index()
    {
        $categorias = DB::table('categorias')
            ->orderBy('nombre')
            ->paginate(10);

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Mostrar formulario de registro.
     */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Guardar nueva categoría.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:categorias,nombre',
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'estado' => [
                'required',
                Rule::in(['activa', 'inactiva']),
            ],
        ]);

        DB::table('categorias')->insert([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('categorias.index')
            ->with('ok', 'Categoría registrada correctamente.');
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(int $id)
    {
        $categoria = DB::table('categorias')
            ->where('id', $id)
            ->first();

        abort_if(!$categoria, 404);

        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar categoría.
     */
    public function update(Request $request, int $id)
    {
        $categoria = DB::table('categorias')
            ->where('id', $id)
            ->first();

        abort_if(!$categoria, 404);

        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias', 'nombre')->ignore($id),
            ],
            'descripcion' => [
                'nullable',
                'string',
            ],
            'estado' => [
                'required',
                Rule::in(['activa', 'inactiva']),
            ],
        ]);

        DB::table('categorias')
            ->where('id', $id)
            ->update([
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? null,
                'estado' => $datos['estado'],
                'updated_at' => now(),
            ]);

        return redirect()->route('categorias.index')
            ->with('ok', 'Categoría actualizada correctamente.');
    }

    /**
     * Desactivar categoría.
     */
    public function destroy(int $id)
    {
        $categoria = DB::table('categorias')
            ->where('id', $id)
            ->first();

        abort_if(!$categoria, 404);

        if ($categoria->estado === 'inactiva') {
            return redirect()->route('categorias.index')
                ->with('ok', 'La categoría ya está desactivada.');
        }

        DB::table('categorias')
            ->where('id', $id)
            ->update([
                'estado' => 'inactiva',
                'updated_at' => now(),
            ]);

        return redirect()->route('categorias.index')
            ->with('ok', 'Categoría desactivada correctamente.');
    }
}
