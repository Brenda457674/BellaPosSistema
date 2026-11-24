<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class PublicoController extends Controller
{
    public function index(Request $request)
    {
        // Categorías activas
        $categorias = Categoria::where('estado', 'activa')
            ->orderBy('nombre')
            ->get();

        // Productos activos
        $consulta = Producto::with(['categoria', 'unidadMedida'])
            ->where('estado', 'activo');

        // Buscar por nombre o marca
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $consulta->where(function ($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('marca', 'LIKE', "%{$buscar}%");
            });
        }

        // Filtrar por categoría
        if ($request->filled('categoria')) {
            $consulta->where('categoria_id', $request->categoria);
        }

        $productos = $consulta
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('publico.index', compact(
            'categorias',
            'productos'
        ));
    }
}
