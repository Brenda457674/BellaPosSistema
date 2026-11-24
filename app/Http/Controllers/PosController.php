<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * Historial de ventas:
     * - Administrador: ve todas las ventas de la tienda.
     * - Vendedor: solo ve sus propias ventas registradas.
     */
    public function index(Request $request)
    {
        $usuario = auth()->user();
        $esAdmin = ($usuario->rol?->nombre === 'Administrador');

        $q = trim((string) $request->query('q', ''));
        $fecha = $request->query('fecha');
        $estado = $request->query('estado');

        $query = DB::table('ventas')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->join('usuarios', 'usuarios.id', '=', 'ventas.usuario_id')
            ->select(
                'ventas.*',
                DB::raw("COALESCE(CONCAT(clientes.nombres, ' ', COALESCE(clientes.apellidos, '')), 'Público General') as cliente_nombre"),
                'clientes.numero_documento as cliente_doc',
                DB::raw("CONCAT(usuarios.nombres, ' ', usuarios.apellidos) as vendedor_nombre")
            );

        // Si es vendedor, solo sus ventas
        if (!$esAdmin) {
            $query->where('ventas.usuario_id', $usuario->id);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('ventas.numero', 'like', "%{$q}%")
                  ->orWhere(DB::raw("CONCAT(ventas.serie, '-', ventas.numero)"), 'like', "%{$q}%")
                  ->orWhere('clientes.nombres', 'like', "%{$q}%")
                  ->orWhere('clientes.numero_documento', 'like', "%{$q}%");
            });
        }

        if ($fecha) {
            $query->whereDate('ventas.fecha_venta', $fecha);
        }

        if ($estado) {
            $query->where('ventas.estado', $estado);
        }

        $ventas = $query->orderByDesc('ventas.fecha_venta')
            ->orderByDesc('ventas.id')
            ->paginate(15)
            ->withQueryString();

        return view('pos.index', compact('ventas', 'q', 'fecha', 'estado', 'esAdmin'));
    }

    /**
     * Pantalla interactiva del Punto de Venta (POS).
     */
    public function create()
    {
        $clientes = DB::table('clientes')
            ->where('estado', 'activo')
            ->orderBy('nombres')
            ->select('id', 'tipo_documento', 'numero_documento', 'nombres', 'apellidos')
            ->get();

        $metodosPago = DB::table('metodos_pago')
            ->where('estado', 'activo')
            ->orderBy('id')
            ->get();

        $productos = DB::table('productos')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->where('productos.estado', 'activo')
            ->select(
                'productos.id',
                'productos.codigo',
                'productos.codigo_barras',
                'productos.nombre',
                'productos.precio_venta',
                'productos.stock_actual',
                DB::raw("COALESCE(unidades_medida.permite_decimales, 0) as permite_decimales"),
                'unidades_medida.abreviatura as unidad'
            )
            ->orderBy('productos.nombre')
            ->get();

        return view('pos.create', compact('clientes', 'metodosPago', 'productos'));
    }

    /**
     * Procesar la venta en una transacción segura:
     * - Bloqueo lockForUpdate
     * - Validación de stock y decimales
     * - Recalcular precios en el servidor
     * - Descontar stock
     * - Registrar salida en Kardex
     * - Registrar venta, detalle_ventas y pagos
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'        => ['nullable', 'integer', 'exists:clientes,id'],
            'metodo_pago_id'    => ['required', 'integer', 'exists:metodos_pago,id'],
            'referencia_pago'   => ['nullable', 'string', 'max:100'],
            'monto_recibido'    => ['nullable', 'numeric', 'min:0'],
            'observaciones'     => ['nullable', 'string', 'max:255'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad'    => ['required', 'numeric', 'gt:0'],
        ], [
            'items.required' => 'El carrito de ventas está vacío. Agrega al menos un producto.',
            'items.min'      => 'El carrito de ventas está vacío. Agrega al menos un producto.',
        ]);

        $ventaId = DB::transaction(function () use ($request) {
            $itemsPost = $request->input('items', []);
            $itemsValidados = [];
            $totalServidor = 0;

            foreach ($itemsPost as $it) {
                $prodId = (int) $it['producto_id'];
                $cant = (float) $it['cantidad'];

                // Bloqueo de fila para evitar condiciones de carrera (concurrency)
                $producto = DB::table('productos')
                    ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
                    ->select(
                        'productos.*',
                        DB::raw("COALESCE(unidades_medida.permite_decimales, 0) as permite_decimales")
                    )
                    ->where('productos.id', $prodId)
                    ->lockForUpdate()
                    ->first();

                if (!$producto || $producto->estado !== 'activo') {
                    throw ValidationException::withMessages([
                        'items' => "El producto '{$producto->nombre}' ya no está disponible para la venta.",
                    ]);
                }

                // Validación de decimales según la unidad de medida
                if ($producto->permite_decimales == 0 && floor($cant) != $cant) {
                    throw ValidationException::withMessages([
                        'items' => "El producto '{$producto->nombre}' se despacha por unidades enteras y no permite decimales.",
                    ]);
                }

                // Validación de stock disponible
                $stockActual = (float) $producto->stock_actual;
                if ($stockActual < $cant) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para '{$producto->nombre}'. Quedan {$stockActual} unidades disponibles en almacén.",
                    ]);
                }

                // Precio real de la base de datos (NUNCA confiar en el navegador)
                $precioReal = (float) $producto->precio_venta;
                $subtotalItem = round($precioReal * $cant, 2);
                $totalServidor += $subtotalItem;

                $itemsValidados[] = [
                    'producto'      => $producto,
                    'cantidad'      => $cant,
                    'precio_real'   => $precioReal,
                    'subtotal_item' => $subtotalItem,
                ];
            }

            // Generar serie y número correlativo
            $serie = 'B001';
            $ultimoNum = DB::table('ventas')->where('serie', $serie)->max('numero');
            $nuevoNum = str_pad(((int) $ultimoNum) + 1, 8, '0', STR_PAD_LEFT);

            // 1. Guardar Venta
            $ventaId = DB::table('ventas')->insertGetId([
                'cliente_id'    => $request->input('cliente_id') ?: null,
                'usuario_id'    => auth()->id(),
                'serie'         => $serie,
                'numero'        => $nuevoNum,
                'fecha_venta'   => now(),
                'subtotal'      => $totalServidor,
                'impuesto'      => 0.00,
                'descuento'     => 0.00,
                'total'         => $totalServidor,
                'estado'        => 'pagada',
                'observaciones' => $request->input('observaciones'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 2. Guardar Detalles, descontar stock y registrar en Kardex
            foreach ($itemsValidados as $iv) {
                $prod = $iv['producto'];
                $cant = $iv['cantidad'];

                DB::table('detalle_ventas')->insert([
                    'venta_id'        => $ventaId,
                    'producto_id'     => $prod->id,
                    'cantidad'        => $cant,
                    'precio_unitario' => $iv['precio_real'],
                    'descuento'       => 0.00,
                    'subtotal'        => $iv['subtotal_item'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                $stockAnterior = (float) $prod->stock_actual;
                $stockNuevo = max(0, $stockAnterior - $cant);

                // Descontar stock
                DB::table('productos')->where('id', $prod->id)->update([
                    'stock_actual' => $stockNuevo,
                    'updated_at'   => now(),
                ]);

                // Registrar salida en Kardex (movimientos_inventario)
                DB::table('movimientos_inventario')->insert([
                    'producto_id'      => $prod->id,
                    'usuario_id'       => auth()->id(),
                    'compra_id'        => null,
                    'venta_id'         => $ventaId,
                    'tipo'             => 'salida',
                    'cantidad'         => $cant,
                    'stock_anterior'   => $stockAnterior,
                    'stock_nuevo'      => $stockNuevo,
                    'motivo'           => "Venta Boleta {$serie}-{$nuevoNum}",
                    'fecha_movimiento' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // 3. Registrar Pago
            DB::table('pagos')->insert([
                'venta_id'       => $ventaId,
                'metodo_pago_id' => $request->input('metodo_pago_id'),
                'usuario_id'     => auth()->id(),
                'fecha_pago'     => now(),
                'monto'          => $totalServidor,
                'referencia'     => $request->input('referencia_pago'),
                'estado'         => 'registrado',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            return $ventaId;
        });

        // Disparar eventos WebSockets en tiempo real
        try {
            $ventaDb = DB::table('ventas')->where('id', $ventaId)->first();
            $usuario = auth()->user();
            $detallesDb = DB::table('detalle_ventas')
                ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
                ->select('detalle_ventas.*', 'productos.nombre as producto_nombre', 'productos.codigo as producto_codigo', 'productos.stock_actual', 'productos.stock_minimo')
                ->where('detalle_ventas.venta_id', $ventaId)
                ->get();

            if ($ventaDb) {
                event(new \App\Events\VentaRealizadaEvent(
                    $ventaId,
                    "{$ventaDb->serie}-{$ventaDb->numero}",
                    (float) $ventaDb->total,
                    $usuario ? ($usuario->nombres . ' ' . ($usuario->apellidos ?? '')) : 'Vendedor POS',
                    count($detallesDb)
                ));

                foreach ($detallesDb as $det) {
                    $stockActual = (float) $det->stock_actual;
                    $stockMinimo = (float) ($det->stock_minimo ?? 5);

                    if ($stockActual <= $stockMinimo) {
                        event(new \App\Events\StockBajoEvent(
                            (int) $det->producto_id,
                            $det->producto_nombre,
                            (string) ($det->producto_codigo ?? ''),
                            $stockActual,
                            $stockMinimo
                        ));
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("WebSocket broadcast error: " . $e->getMessage());
        }

        // Calcular vuelto opcional si se envió monto_recibido
        $montoRecibido = (float) $request->input('monto_recibido', 0);
        $totalVenta = (float) DB::table('ventas')->where('id', $ventaId)->value('total');
        $vuelto = ($montoRecibido > $totalVenta) ? ($montoRecibido - $totalVenta) : 0;

        $msg = 'Venta cobrada con éxito. Comprobante listo para imprimir.';
        if ($vuelto > 0) {
            $msg .= ' Vuelto: S/ ' . number_format($vuelto, 2);
        }

        return redirect()->route('pos.show', $ventaId)->with('ok', $msg);
    }

    /**
     * Comprobante / Ticket de venta imprimible.
     */
    public function show(int $id)
    {
        $usuario = auth()->user();
        $esAdmin = ($usuario->rol?->nombre === 'Administrador');

        $venta = DB::table('ventas')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->join('usuarios', 'usuarios.id', '=', 'ventas.usuario_id')
            ->select(
                'ventas.*',
                'clientes.tipo_documento as cliente_tipo_doc',
                'clientes.numero_documento as cliente_doc',
                DB::raw("COALESCE(CONCAT(clientes.nombres, ' ', COALESCE(clientes.apellidos, '')), 'Público General') as cliente_nombre"),
                'clientes.direccion as cliente_dir',
                DB::raw("CONCAT(usuarios.nombres, ' ', usuarios.apellidos) as vendedor_nombre")
            )
            ->where('ventas.id', $id)
            ->first() ?? abort(404, 'Venta no encontrada.');

        // Vendedor solo puede ver sus propias ventas
        if (!$esAdmin && $venta->usuario_id !== $usuario->id) {
            abort(403, 'No tienes permiso para ver esta venta.');
        }

        $detalles = DB::table('detalle_ventas')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->select(
                'detalle_ventas.*',
                'productos.nombre as producto_nombre',
                'productos.codigo as producto_codigo',
                'unidades_medida.abreviatura as unidad'
            )
            ->where('detalle_ventas.venta_id', $id)
            ->get();

        $pago = DB::table('pagos')
            ->join('metodos_pago', 'metodos_pago.id', '=', 'pagos.metodo_pago_id')
            ->select('pagos.*', 'metodos_pago.nombre as metodo_nombre')
            ->where('pagos.venta_id', $id)
            ->first();

        $tienda = DB::table('configuracion_tienda')->first();

        return view('pos.show', compact('venta', 'detalles', 'pago', 'tienda', 'esAdmin'));
    }

    /**
     * Anular una venta (Solo Administrador).
     * Devuelve el stock y registra movimiento "devolucion" en Kardex.
     */
    public function anular(int $id)
    {
        if (auth()->user()->rol?->nombre !== 'Administrador') {
            abort(403, 'Solo el Administrador puede anular ventas.');
        }

        $venta = DB::table('ventas')->where('id', $id)->first() ?? abort(404);

        if ($venta->estado === 'anulada') {
            return redirect()->route('pos.show', $id)
                ->with('error', 'Esta venta ya fue anulada previamente.');
        }

        DB::transaction(function () use ($venta, $id) {
            $detalles = DB::table('detalle_ventas')->where('venta_id', $id)->get();

            foreach ($detalles as $det) {
                $producto = DB::table('productos')->where('id', $det->producto_id)->lockForUpdate()->first();
                $stockAnterior = (float) $producto->stock_actual;
                $stockNuevo = $stockAnterior + (float) $det->cantidad;

                // Devolver stock
                DB::table('productos')->where('id', $det->producto_id)->update([
                    'stock_actual' => $stockNuevo,
                    'updated_at'   => now(),
                ]);

                // Registrar en kardex con tipo 'devolucion'
                DB::table('movimientos_inventario')->insert([
                    'producto_id'      => $det->producto_id,
                    'usuario_id'       => auth()->id(),
                    'compra_id'        => null,
                    'venta_id'         => $id,
                    'tipo'             => 'devolucion',
                    'cantidad'         => $det->cantidad,
                    'stock_anterior'   => $stockAnterior,
                    'stock_nuevo'      => $stockNuevo,
                    'motivo'           => "Devolución por anulación de Venta {$venta->serie}-{$venta->numero}",
                    'fecha_movimiento' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // Anular venta y pago
            DB::table('ventas')->where('id', $id)->update([
                'estado'     => 'anulada',
                'updated_at' => now(),
            ]);

            DB::table('pagos')->where('venta_id', $id)->update([
                'estado'     => 'anulado',
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('pos.show', $id)
            ->with('ok', 'Venta anulada con éxito y productos devueltos al stock.');
    }
}
