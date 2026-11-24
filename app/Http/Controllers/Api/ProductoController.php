<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductoRequest;
use App\Http\Requests\Api\UpdateProductoRequest;
use App\Http\Resources\Api\ProductoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    private function consulta()
    {
        return DB::table('productos')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->select(
                'productos.*',
                'categorias.nombre as categoria_nombre',
                'unidades_medida.nombre as unidad_nombre',
                'unidades_medida.abreviatura as unidad_abreviatura'
            );
    }

    /** GET /api/productos?q=&categoria_id=&estado=&stock=bajo&per_page= */
    public function index(Request $request)
    {
        $query = $this->consulta();

        if ($request->filled('q')) {
            $b = trim($request->query('q'));
            $query->where(function ($w) use ($b) {
                $w->where('productos.nombre', 'like', "%{$b}%")
                  ->orWhere('productos.codigo', 'like', "%{$b}%")
                  ->orWhere('productos.codigo_barras', 'like', "%{$b}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('productos.categoria_id', $request->query('categoria_id'));
        }

        if (in_array($request->query('estado'), ['activo', 'inactivo'], true)) {
            $query->where('productos.estado', $request->query('estado'));
        }

        if ($request->query('stock') === 'bajo') {
            $query->whereColumn('productos.stock_actual', '<=', 'productos.stock_minimo');
        }

        $porPagina = min(max((int) $request->query('per_page', 10), 1), 50);

        return ProductoResource::collection(
            $query->orderBy('productos.nombre')->paginate($porPagina)->withQueryString()
        );
    }

    /** GET /api/productos/{producto} */
    public function show(int $producto)
    {
        $fila = $this->consulta()->where('productos.id', $producto)->first()
            ?? abort(404, 'Producto no encontrado.');

        return new ProductoResource($fila);
    }

    /** POST /api/productos */
    public function store(StoreProductoRequest $request)
    {
        $d = $request->validated();

        $id = DB::table('productos')->insertGetId([
            'codigo'               => $d['codigo'],
            'codigo_barras'        => $d['codigo_barras'] ?? null,
            'nombre'               => $d['nombre'],
            'categoria_id'         => $d['categoria_id'],
            'unidad_medida_id'     => $d['unidad_medida_id'],
            'descripcion'          => $d['descripcion'] ?? null,
            'marca'                => $d['marca'] ?? null,
            'precio_compra'        => $d['precio_compra'],
            'precio_venta'         => $d['precio_venta'],
            'stock_actual'         => $d['stock_actual'] ?? 0,
            'stock_minimo'         => $d['stock_minimo'] ?? 0,
            'controla_vencimiento' => $request->boolean('controla_vencimiento'),
            'estado'               => $d['estado'] ?? 'activo',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        $fila = $this->consulta()->where('productos.id', $id)->first();

        return (new ProductoResource($fila))->response()->setStatusCode(201);
    }

    /** PUT/PATCH /api/productos/{producto} */
    public function update(UpdateProductoRequest $request, int $producto)
    {
        DB::table('productos')->where('id', $producto)->first()
            ?? abort(404, 'Producto no encontrado.');

        $d = $request->validated();

        DB::table('productos')->where('id', $producto)->update([
            'codigo'               => $d['codigo'],
            'codigo_barras'        => $d['codigo_barras'] ?? null,
            'nombre'               => $d['nombre'],
            'categoria_id'         => $d['categoria_id'],
            'unidad_medida_id'     => $d['unidad_medida_id'],
            'descripcion'          => $d['descripcion'] ?? null,
            'marca'                => $d['marca'] ?? null,
            'precio_compra'        => $d['precio_compra'],
            'precio_venta'         => $d['precio_venta'],
            'stock_minimo'         => $d['stock_minimo'] ?? 0,
            'controla_vencimiento' => $request->boolean('controla_vencimiento'),
            'estado'               => $d['estado'],
            'updated_at'           => now(),
        ]);

        $fila = $this->consulta()->where('productos.id', $producto)->first();

        return new ProductoResource($fila);
    }

    /** DELETE /api/productos/{producto}  (solo Administrador: desactiva) */
    public function destroy(int $producto): JsonResponse
    {
        DB::table('productos')->where('id', $producto)->first()
            ?? abort(404, 'Producto no encontrado.');

        DB::table('productos')->where('id', $producto)->update([
            'estado'     => 'inactivo',
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Producto desactivado correctamente.'], 200);
    }
}
