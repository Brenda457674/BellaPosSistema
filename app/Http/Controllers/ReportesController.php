<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    /**
     * Reportes generales del minimarket (Exclusivo Administrador).
     */
    public function index(Request $request)
    {
        $fechaDesde = $request->query('desde', now()->startOfMonth()->toDateString());
        $fechaHasta = $request->query('hasta', now()->toDateString());

        // 1. Resumen de Ventas en el rango
        $resumenVentas = DB::table('ventas')
            ->where('estado', 'pagada')
            ->whereDate('fecha_venta', '>=', $fechaDesde)
            ->whereDate('fecha_venta', '<=', $fechaHasta)
            ->select(
                DB::raw('COUNT(*) as total_transacciones'),
                DB::raw('COALESCE(SUM(total), 0) as monto_total'),
                DB::raw('COALESCE(AVG(total), 0) as ticket_promedio')
            )
            ->first();

        // 2. Productos Más Vendidos en el rango
        $topProductos = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->where('ventas.estado', 'pagada')
            ->whereDate('ventas.fecha_venta', '>=', $fechaDesde)
            ->whereDate('ventas.fecha_venta', '<=', $fechaHasta)
            ->select(
                'productos.id',
                'productos.codigo',
                'productos.nombre',
                'productos.stock_actual',
                'unidades_medida.abreviatura as unidad',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_ingreso')
            )
            ->groupBy('productos.id', 'productos.codigo', 'productos.nombre', 'productos.stock_actual', 'unidades_medida.abreviatura')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // 3. Ventas por Método de Pago en el rango
        $ventasPorMetodo = DB::table('pagos')
            ->join('ventas', 'ventas.id', '=', 'pagos.venta_id')
            ->join('metodos_pago', 'metodos_pago.id', '=', 'pagos.metodo_pago_id')
            ->where('ventas.estado', 'pagada')
            ->where('pagos.estado', 'registrado')
            ->whereDate('ventas.fecha_venta', '>=', $fechaDesde)
            ->whereDate('ventas.fecha_venta', '<=', $fechaHasta)
            ->select(
                'metodos_pago.nombre as metodo',
                DB::raw('COUNT(*) as cantidad_operaciones'),
                DB::raw('SUM(pagos.monto) as total_monto')
            )
            ->groupBy('metodos_pago.id', 'metodos_pago.nombre')
            ->orderByDesc('total_monto')
            ->get();

        // 4. Productos con Stock Bajo / Crítico (actual)
        $stockBajo = DB::table('productos')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->where('productos.estado', 'activo')
            ->whereColumn('productos.stock_actual', '<=', 'productos.stock_minimo')
            ->select(
                'productos.*',
                'categorias.nombre as categoria_nombre',
                'unidades_medida.abreviatura as unidad'
            )
            ->orderBy('productos.stock_actual')
            ->limit(15)
            ->get();

        return view('reportes.index', compact(
            'fechaDesde',
            'fechaHasta',
            'resumenVentas',
            'topProductos',
            'ventasPorMetodo',
            'stockBajo'
        ));
    }
}
