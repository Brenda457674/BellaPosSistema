<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductosController extends Controller
{
    /**
     * Listar productos con búsqueda, filtros y paginación.
     */
    public function index(Request $request)
    {
        $query = $this->productosQuery();

        if ($request->filled('q')) {
            $busqueda = trim($request->input('q'));

            $query->where(function ($q) use ($busqueda) {
                $q->where('productos.nombre', 'like', "%{$busqueda}%")
                    ->orWhere('productos.codigo', 'like', "%{$busqueda}%")
                    ->orWhere(
                        'productos.codigo_barras',
                        'like',
                        "%{$busqueda}%"
                    );
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where(
                'productos.categoria_id',
                $request->input('categoria_id')
            );
        }

        if (in_array($request->input('estado'), ['activo', 'inactivo'], true)) {
            $query->where('productos.estado', $request->input('estado'));
        }

        if ($request->input('stock') === 'bajo') {
            $query->whereColumn(
                'productos.stock_actual',
                '<=',
                'productos.stock_minimo'
            );
        }

        $productos = $query
            ->orderBy('productos.nombre')
            ->paginate(10)
            ->withQueryString();

        $categorias = DB::table('categorias')
            ->orderBy('nombre')
            ->get();

        return view('admin.productos.index', compact(
            'productos',
            'categorias'
        ));
    }

    /**
     * Mostrar formulario de registro.
     */
    public function create()
    {
       if (!in_array(Auth::user()->rol?->nombre, ['Administrador', 'Almacenero'], true)) {
    abort(403, 'No tienes permiso para crear productos.');
}

        $categorias = DB::table('categorias')
            ->where('estado', 'activa')
            ->orderBy('nombre')
            ->get();

        $unidades = $this->obtenerUnidades();

        return view('admin.productos.create', compact(
            'categorias',
            'unidades'
        ));
    }

    /**
     * Guardar un producto nuevo.
     */
    public function store(Request $request)
    {
        if (!in_array(Auth::user()->rol?->nombre, ['Administrador', 'Almacenero'], true)) {
    abort(403, 'No tienes permiso para crear productos.');
}

        $datos = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:255',
                'unique:productos,codigo',
            ],
            'codigo_barras' => [
                'nullable',
                'string',
                'max:255',
                'unique:productos,codigo_barras',
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],
            'unidad_medida_id' => [
                'required',
                'integer',
                'exists:unidades_medida,id',
            ],
            'descripcion' => ['nullable', 'string'],
            'marca' => ['nullable', 'string', 'max:255'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'controla_vencimiento' => ['nullable', 'boolean'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ]);

        // Solo se guardan campos autorizados.
        $producto = [
            'codigo' => $datos['codigo'],
            'codigo_barras' => $datos['codigo_barras'] ?? null,
            'nombre' => $datos['nombre'],
            'categoria_id' => $datos['categoria_id'],
            'unidad_medida_id' => $datos['unidad_medida_id'],
            'descripcion' => $datos['descripcion'] ?? null,
            'marca' => $datos['marca'] ?? null,
            'precio_compra' => $datos['precio_compra'],
            'precio_venta' => $datos['precio_venta'],
            'stock_actual' => $datos['stock_actual'],
            'stock_minimo' => $datos['stock_minimo'],
            'controla_vencimiento' => $request->boolean(
                'controla_vencimiento'
            ),
            'estado' => $datos['estado'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('productos')->insert($producto);

        return redirect()->route('admin.productos.index')
            ->with('ok', 'Producto registrado correctamente.');
    }

    /**
     * Mostrar detalle del producto.
     */
    public function show(int $id)
    {
        $producto = $this->productosQuery()
            ->where('productos.id', $id)
            ->firstOrFail();

        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(int $id)
    {
        if (!in_array(Auth::user()->rol?->nombre, ['Administrador', 'Almacenero'], true)) {
    abort(403, 'No tienes permiso para crear productos.');
}

        $producto = DB::table('productos')
            ->where('id', $id)
            ->firstOrFail();

        $categorias = DB::table('categorias')
            ->orderBy('nombre')
            ->get();

        $unidades = $this->obtenerUnidades();

        return view('admin.productos.edit', compact(
            'producto',
            'categorias',
            'unidades'
        ));
    }

    /**
     * Actualizar datos generales sin modificar el stock actual.
     */
    public function update(Request $request, int $id)
    {
        if (!in_array(Auth::user()->rol?->nombre, ['Administrador', 'Almacenero'], true)) {
    abort(403, 'No tienes permiso para crear productos.');
}

        $producto = DB::table('productos')
            ->where('id', $id)
            ->firstOrFail();

        $datos = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos', 'codigo')->ignore($id),
            ],
            'codigo_barras' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('productos', 'codigo_barras')->ignore($id),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'categoria_id' => [
                'required',
                'integer',
                'exists:categorias,id',
            ],
            'unidad_medida_id' => [
                'required',
                'integer',
                'exists:unidades_medida,id',
            ],
            'descripcion' => ['nullable', 'string'],
            'marca' => ['nullable', 'string', 'max:255'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'controla_vencimiento' => ['nullable', 'boolean'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ]);

        DB::table('productos')
            ->where('id', $id)
            ->update([
                'codigo' => $datos['codigo'],
                'codigo_barras' => $datos['codigo_barras'] ?? null,
                'nombre' => $datos['nombre'],
                'categoria_id' => $datos['categoria_id'],
                'unidad_medida_id' => $datos['unidad_medida_id'],
                'descripcion' => $datos['descripcion'] ?? null,
                'marca' => $datos['marca'] ?? null,
                'precio_compra' => $datos['precio_compra'],
                'precio_venta' => $datos['precio_venta'],
                'stock_minimo' => $datos['stock_minimo'],
                'controla_vencimiento' => $request->boolean(
                    'controla_vencimiento'
                ),
                'estado' => $datos['estado'],
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.productos.index')
            ->with('ok', 'Producto actualizado correctamente.');
    }

    /**
     * Desactivar sin eliminar físicamente el producto.
     */
    public function destroy(int $id)
    {
        if (!in_array(Auth::user()->rol?->nombre, ['Administrador', 'Almacenero'], true)) {
    abort(403, 'No tienes permiso para crear productos.');
}

        $producto = DB::table('productos')
            ->where('id', $id)
            ->firstOrFail();

        if ($producto->estado === 'inactivo') {
            return redirect()->route('admin.productos.index')
                ->with('ok', 'El producto ya estaba desactivado.');
        }

        DB::table('productos')
            ->where('id', $id)
            ->update([
                'estado' => 'inactivo',
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.productos.index')
            ->with('ok', 'Producto desactivado correctamente.');
    }

    /**
     * Consulta con nombres de categoría y unidad.
     */
    private function productosQuery()
    {
        $query = DB::table('productos')
            ->leftJoin(
                'categorias',
                'categorias.id',
                '=',
                'productos.categoria_id'
            )
            ->select(
                'productos.*',
                'categorias.nombre as categoria_nombre'
            );

        if (
            Schema::hasTable('unidades_medida')
            && Schema::hasColumn('unidades_medida', 'id')
            && Schema::hasColumn('productos', 'unidad_medida_id')
        ) {
            $columnas = Schema::getColumnListing('unidades_medida');

            $columnaNombre = null;

            foreach ([
                'nombre',
                'nombre_unidad',
                'unidad',
                'abreviatura',
                'simbolo',
            ] as $posibleColumna) {
                if (in_array($posibleColumna, $columnas, true)) {
                    $columnaNombre = $posibleColumna;
                    break;
                }
            }

            $query->leftJoin(
                'unidades_medida as um',
                'um.id',
                '=',
                'productos.unidad_medida_id'
            );

            if ($columnaNombre) {
                $query->addSelect(
                    "um.{$columnaNombre} as unidad_nombre"
                );
            }
        }

        return $query;
    }

    /**
     * Obtener unidades reales sin asumir cómo se llama su descripción.
     */
    private function obtenerUnidades()
    {
        if (!Schema::hasTable('unidades_medida')) {
            return collect();
        }

        $unidades = DB::table('unidades_medida');
        $columnas = Schema::getColumnListing('unidades_medida');

        foreach ([
            'nombre',
            'nombre_unidad',
            'unidad',
            'abreviatura',
            'simbolo',
        ] as $columna) {
            if (in_array($columna, $columnas, true)) {
                return $unidades->orderBy($columna)->get();
            }
        }

        return $unidades->get();
    }
}
