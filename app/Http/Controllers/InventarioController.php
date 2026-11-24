<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventarioController extends Controller
{
    /**
     * Vista general de existencias y alertas de stock.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoriaId = $request->query('categoria_id');
        $filtroStock = $request->query('filtro_stock', 'todos');

        $query = DB::table('productos')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->select(
                'productos.*',
                'categorias.nombre as categoria_nombre',
                'unidades_medida.nombre as unidad_nombre',
                'unidades_medida.abreviatura as unidad_abrev'
            );

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('productos.nombre', 'like', "%{$q}%")
                  ->orWhere('productos.codigo', 'like', "%{$q}%")
                  ->orWhere('productos.codigo_barras', 'like', "%{$q}%");
            });
        }

        if ($categoriaId) {
            $query->where('productos.categoria_id', $categoriaId);
        }

        if ($filtroStock === 'bajo') {
            $query->whereColumn('productos.stock_actual', '<=', 'productos.stock_minimo');
        } elseif ($filtroStock === 'agotado') {
            $query->where('productos.stock_actual', '<=', 0);
        }

        $productos = $query->orderBy('productos.stock_actual', 'asc')
            ->orderBy('productos.nombre', 'asc')
            ->paginate(15)
            ->withQueryString();

        $categorias = DB::table('categorias')->orderBy('nombre')->get();

        // KPIs resumen de inventario
        $kpis = [
            'total_productos' => DB::table('productos')->where('estado', 'activo')->count(),
            'stock_bajo'      => DB::table('productos')->where('estado', 'activo')->whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
            'agotados'        => DB::table('productos')->where('estado', 'activo')->where('stock_actual', '<=', 0)->count(),
            'valor_total'     => DB::table('productos')->where('estado', 'activo')->sum(DB::raw('stock_actual * precio_compra')),
        ];

        return view('inventario.index', compact('productos', 'categorias', 'q', 'categoriaId', 'filtroStock', 'kpis'));
    }

    /**
     * Historial de movimientos de inventario (Kardex).
     */
    public function kardex(Request $request)
    {
        $productoId = $request->query('producto_id');
        $tipo = $request->query('tipo');
        $fechaDesde = $request->query('fecha_desde');
        $fechaHasta = $request->query('fecha_hasta');

        $query = DB::table('movimientos_inventario')
            ->join('productos', 'productos.id', '=', 'movimientos_inventario.producto_id')
            ->join('usuarios', 'usuarios.id', '=', 'movimientos_inventario.usuario_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->select(
                'movimientos_inventario.*',
                'productos.nombre as producto_nombre',
                'productos.codigo as producto_codigo',
                'unidades_medida.abreviatura as unidad',
                DB::raw("CONCAT(usuarios.nombres, ' ', usuarios.apellidos) as usuario_nombre")
            );

        if ($productoId) {
            $query->where('movimientos_inventario.producto_id', $productoId);
        }

        if ($tipo) {
            $query->where('movimientos_inventario.tipo', $tipo);
        }

        if ($fechaDesde) {
            $query->whereDate('movimientos_inventario.fecha_movimiento', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('movimientos_inventario.fecha_movimiento', '<=', $fechaHasta);
        }

        $movimientos = $query->orderByDesc('movimientos_inventario.fecha_movimiento')
            ->orderByDesc('movimientos_inventario.id')
            ->paginate(20)
            ->withQueryString();

        $productos = DB::table('productos')->orderBy('nombre')->select('id', 'codigo', 'nombre')->get();

        return view('inventario.kardex', compact('movimientos', 'productos', 'productoId', 'tipo', 'fechaDesde', 'fechaHasta'));
    }

    /**
     * Formulario para registrar un ajuste manual de stock.
     */
    public function ajuste(Request $request)
    {
        $productoSeleccionadoId = $request->query('producto_id');
        $productos = DB::table('productos')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->select('id', 'codigo', 'nombre', 'stock_actual')
            ->get();

        return view('inventario.ajuste', compact('productos', 'productoSeleccionadoId'));
    }

    /**
     * Guardar el ajuste de inventario en transacción atómica.
     */
    public function storeAjuste(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => ['required', 'integer', 'exists:productos,id'],
            'tipo'        => ['required', Rule::in(['ajuste_positivo', 'ajuste_negativo'])],
            'cantidad'    => ['required', 'numeric', 'gt:0'],
            'motivo'      => ['required', 'string', 'max:255'],
        ], [
            'motivo.required' => 'Debes indicar el motivo del ajuste (ej. Merma, producto vencido, conteo físico).',
        ]);

        DB::transaction(function () use ($datos) {
            $producto = DB::table('productos')
                ->where('id', $datos['producto_id'])
                ->lockForUpdate()
                ->first();

            $stockAnterior = (float) $producto->stock_actual;
            $cantidad = (float) $datos['cantidad'];

            if ($datos['tipo'] === 'ajuste_negativo') {
                if ($stockAnterior < $cantidad) {
                    throw ValidationException::withMessages([
                        'cantidad' => "No puedes restar {$cantidad} unidades porque el stock actual es solo de {$stockAnterior}.",
                    ]);
                }
                $stockNuevo = $stockAnterior - $cantidad;
            } else {
                $stockNuevo = $stockAnterior + $cantidad;
            }

            // 1. Actualizar stock del producto
            DB::table('productos')->where('id', $producto->id)->update([
                'stock_actual' => $stockNuevo,
                'updated_at'   => now(),
            ]);

            // 2. Registrar en kardex
            DB::table('movimientos_inventario')->insert([
                'producto_id'      => $producto->id,
                'usuario_id'       => auth()->id(),
                'compra_id'        => null,
                'venta_id'         => null,
                'tipo'             => $datos['tipo'],
                'cantidad'         => $cantidad,
                'stock_anterior'   => $stockAnterior,
                'stock_nuevo'      => $stockNuevo,
                'motivo'           => $datos['motivo'],
                'fecha_movimiento' => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        });

        return redirect()->route('inventario.index')
            ->with('ok', 'Ajuste de inventario registrado y stock actualizado.');
    }
}
