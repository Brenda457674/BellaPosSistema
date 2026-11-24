<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVentaRequest;
use App\Http\Resources\Api\VentaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    private function esAdmin(Request $request): bool
    {
        return $request->user()->rol?->nombre === 'Administrador';
    }

    private function consulta()
    {
        return DB::table('ventas')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->join('usuarios', 'usuarios.id', '=', 'ventas.usuario_id')
            ->select(
                'ventas.*',
                DB::raw("COALESCE(CONCAT(clientes.nombres, ' ', COALESCE(clientes.apellidos, '')), 'Público General') as cliente_nombre"),
                DB::raw("CONCAT(usuarios.nombres, ' ', usuarios.apellidos) as vendedor_nombre")
            );
    }

    /** Venta completa con detalles y pago. */
    private function cargar(int $id): ?object
    {
        $venta = $this->consulta()->where('ventas.id', $id)->first();

        if (!$venta) {
            return null;
        }

        $venta->detalles = DB::table('detalle_ventas')
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

        $venta->pago = DB::table('pagos')
            ->join('metodos_pago', 'metodos_pago.id', '=', 'pagos.metodo_pago_id')
            ->select('pagos.*', 'metodos_pago.nombre as metodo_nombre')
            ->where('pagos.venta_id', $id)
            ->first();

        return $venta;
    }

    /** GET /api/ventas  (Vendedor: solo las suyas) */
    public function index(Request $request)
    {
        $query = $this->consulta();

        if (!$this->esAdmin($request)) {
            $query->where('ventas.usuario_id', $request->user()->id);
        }

        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($w) use ($q) {
                $w->where('ventas.numero', 'like', "%{$q}%")
                  ->orWhere(DB::raw("CONCAT(ventas.serie, '-', ventas.numero)"), 'like', "%{$q}%")
                  ->orWhere('clientes.nombres', 'like', "%{$q}%")
                  ->orWhere('clientes.numero_documento', 'like', "%{$q}%");
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('ventas.fecha_venta', $request->query('fecha'));
        }

        if (in_array($request->query('estado'), ['pendiente', 'pagada', 'anulada'], true)) {
            $query->where('ventas.estado', $request->query('estado'));
        }

        $porPagina = min(max((int) $request->query('per_page', 15), 1), 50);

        return VentaResource::collection(
            $query->orderByDesc('ventas.fecha_venta')
                ->orderByDesc('ventas.id')
                ->paginate($porPagina)
                ->withQueryString()
        );
    }

    /** GET /api/ventas/{venta} */
    public function show(Request $request, int $venta)
    {
        $fila = $this->cargar($venta) ?? abort(404, 'Venta no encontrada.');

        if (!$this->esAdmin($request) && (int) $fila->usuario_id !== (int) $request->user()->id) {
            abort(403, 'No tienes permiso para ver esta venta.');
        }

        return new VentaResource($fila);
    }

    /** POST /api/ventas  (cobrar: misma lógica transaccional del POS) */
    public function store(StoreVentaRequest $request)
    {
        $usuarioId = $request->user()->id;

        $ventaId = DB::transaction(function () use ($request, $usuarioId) {

            // 1. Juntar productos repetidos y ordenarlos (evita bloqueos cruzados)
            $pedido = [];
            foreach ($request->input('items') as $it) {
                $pid = (int) $it['producto_id'];
                $pedido[$pid] = ($pedido[$pid] ?? 0) + (float) $it['cantidad'];
            }
            ksort($pedido);

            // 2. Validar con bloqueo de fila y precios reales del servidor
            $items = [];
            $total = 0;

            foreach ($pedido as $prodId => $cant) {
                $cant = round($cant, 3);

                $producto = DB::table('productos')
                    ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
                    ->select(
                        'productos.*',
                        DB::raw('COALESCE(unidades_medida.permite_decimales, 0) as permite_decimales')
                    )
                    ->where('productos.id', $prodId)
                    ->lockForUpdate()
                    ->first();

                if (!$producto) {
                    throw ValidationException::withMessages([
                        'items' => "El producto con id {$prodId} no existe.",
                    ]);
                }

                if ($producto->estado !== 'activo') {
                    throw ValidationException::withMessages([
                        'items' => "El producto '{$producto->nombre}' ya no está disponible para la venta.",
                    ]);
                }

                if ((int) $producto->permite_decimales === 0 && floor($cant) != $cant) {
                    throw ValidationException::withMessages([
                        'items' => "El producto '{$producto->nombre}' se vende por unidades enteras y no permite decimales.",
                    ]);
                }

                $stock = (float) $producto->stock_actual;
                if ($stock < $cant) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para '{$producto->nombre}'. Quedan {$stock} disponibles.",
                    ]);
                }

                $precio = (float) $producto->precio_venta;
                $subtotal = round($precio * $cant, 2);
                $total += $subtotal;

                $items[] = [
                    'producto' => $producto,
                    'cantidad' => $cant,
                    'precio'   => $precio,
                    'subtotal' => $subtotal,
                ];
            }

            // 3. Serie y número correlativo (con bloqueo para no repetir números)
            $serie = 'B001';
            $ultimo = DB::table('ventas')->where('serie', $serie)->lockForUpdate()->max('numero');
            $numero = str_pad(((int) $ultimo) + 1, 8, '0', STR_PAD_LEFT);

            // 4. Guardar la venta
            $ventaId = DB::table('ventas')->insertGetId([
                'cliente_id'    => $request->input('cliente_id') ?: null,
                'usuario_id'    => $usuarioId,
                'serie'         => $serie,
                'numero'        => $numero,
                'fecha_venta'   => now(),
                'subtotal'      => $total,
                'impuesto'      => 0.00,
                'descuento'     => 0.00,
                'total'         => $total,
                'estado'        => 'pagada',
                'observaciones' => $request->input('observaciones'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 5. Detalle, descuento de stock y Kardex
            foreach ($items as $iv) {
                $prod = $iv['producto'];
                $cant = $iv['cantidad'];

                DB::table('detalle_ventas')->insert([
                    'venta_id'        => $ventaId,
                    'producto_id'     => $prod->id,
                    'cantidad'        => $cant,
                    'precio_unitario' => $iv['precio'],
                    'descuento'       => 0.00,
                    'subtotal'        => $iv['subtotal'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                $anterior = (float) $prod->stock_actual;
                $nuevo = max(0, $anterior - $cant);

                DB::table('productos')->where('id', $prod->id)->update([
                    'stock_actual' => $nuevo,
                    'updated_at'   => now(),
                ]);

                DB::table('movimientos_inventario')->insert([
                    'producto_id'      => $prod->id,
                    'usuario_id'       => $usuarioId,
                    'compra_id'        => null,
                    'venta_id'         => $ventaId,
                    'tipo'             => 'salida',
                    'cantidad'         => $cant,
                    'stock_anterior'   => $anterior,
                    'stock_nuevo'      => $nuevo,
                    'motivo'           => "Venta Boleta {$serie}-{$numero}",
                    'fecha_movimiento' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // 6. Pago
            DB::table('pagos')->insert([
                'venta_id'       => $ventaId,
                'metodo_pago_id' => $request->input('metodo_pago_id'),
                'usuario_id'     => $usuarioId,
                'fecha_pago'     => now(),
                'monto'          => $total,
                'referencia'     => $request->input('referencia_pago'),
                'estado'         => 'registrado',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            return $ventaId;
        });

        $venta = $this->cargar($ventaId);

        // Disparar eventos WebSockets en tiempo real
        try {
            event(new \App\Events\VentaRealizadaEvent(
                $ventaId,
                "{$venta->serie}-{$venta->numero}",
                (float) $venta->total,
                $venta->vendedor_nombre ?? 'Vendedor',
                count($venta->detalles ?? [])
            ));

            if (!empty($venta->detalles)) {
                foreach ($venta->detalles as $det) {
                    $productoDb = DB::table('productos')->where('id', $det->producto_id)->first();
                    if ($productoDb) {
                        $stockActual = (float) $productoDb->stock_actual;
                        $stockMinimo = (float) ($productoDb->stock_minimo ?? 5);

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
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("WebSocket broadcast error: " . $e->getMessage());
        }

        $recibido = (float) $request->input('monto_recibido', 0);
        $vuelto = $recibido > (float) $venta->total ? round($recibido - (float) $venta->total, 2) : 0;

        return (new VentaResource($venta))
            ->additional([
                'message' => 'Venta cobrada con éxito.',
                'vuelto'  => $vuelto,
            ])
            ->response()
            ->setStatusCode(201);
    }
}
