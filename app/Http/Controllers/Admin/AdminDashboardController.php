<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Mostrar el dashboard del administrador con datos reales.
     */
    public function index()
    {
        $usuario = Auth::user();

        // 1. KPIs del día y globales
        $totalProductos = DB::table('productos')->where('estado', 'activo')->count();
        $productosStockBajo = DB::table('productos')->where('estado', 'activo')->whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $ventasDelDia = DB::table('ventas')->whereDate('fecha_venta', today())->where('estado', 'pagada')->count();
        $montoVentasDelDia = DB::table('ventas')->whereDate('fecha_venta', today())->where('estado', 'pagada')->sum('total');
        $usuariosActivos = DB::table('usuarios')->where('estado', 'activo')->count();

        // 2. Ventas recientes reales
        $ventasRecientes = DB::table('ventas')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->select(
                'ventas.*',
                DB::raw("COALESCE(CONCAT(clientes.nombres, ' ', COALESCE(clientes.apellidos, '')), 'Público General') as cliente_nombre")
            )
            ->orderByDesc('ventas.fecha_venta')
            ->limit(6)
            ->get();

        // 3. Productos más vendidos
        $topProductos = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', 'pagada')
            ->select(
                'productos.nombre',
                'productos.codigo',
                'productos.stock_actual',
                DB::raw('SUM(detalle_ventas.cantidad) as total_cantidad'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_monto')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.codigo', 'productos.stock_actual')
            ->orderByDesc('total_cantidad')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'usuario'            => $usuario,
            'totalProductos'     => $totalProductos,
            'productosStockBajo' => $productosStockBajo,
            'ventasDelDia'       => $ventasDelDia,
            'montoVentasDelDia'  => $montoVentasDelDia,
            'usuariosActivos'    => $usuariosActivos,
            'ventasRecientes'    => $ventasRecientes,
            'topProductos'       => $topProductos,
        ]);
    }
}
