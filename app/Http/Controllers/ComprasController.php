<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComprasController extends Controller
{
    /**
     * Listado de compras registradas.
     * Roles: Administrador y Almacenero.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado');
        $proveedorId = $request->query('proveedor_id');

        $compras = DB::table('compras')
            ->join('proveedores', 'proveedores.id', '=', 'compras.proveedor_id')
            ->join('usuarios', 'usuarios.id', '=', 'compras.usuario_id')
            ->select(
                'compras.*',
                'proveedores.razon_social as proveedor_nombre',
                'proveedores.numero_documento as proveedor_doc',
                DB::raw("CONCAT(usuarios.nombres, ' ', usuarios.apellidos) as usuario_nombre")
            )
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('compras.numero_comprobante', 'like', "%{$q}%")
                      ->orWhere('proveedores.razon_social', 'like', "%{$q}%");
                });
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('compras.estado', $estado);
            })
            ->when($proveedorId, function ($query) use ($proveedorId) {
                $query->where('compras.proveedor_id', $proveedorId);
            })
            ->orderByDesc('compras.fecha_compra')
            ->orderByDesc('compras.id')
            ->paginate(15)
            ->withQueryString();

        $proveedores = DB::table('proveedores')->where('estado', 'activo')->orderBy('razon_social')->get();

        return view('compras.index', compact('compras', 'proveedores', 'q', 'estado', 'proveedorId'));
    }

    /**
     * Formulario para registrar una nueva compra de mercadería.
     */
    public function create()
    {
        $proveedores = DB::table('proveedores')->where('estado', 'activo')->orderBy('razon_social')->get();
        $productos = DB::table('productos')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->select('id', 'codigo', 'nombre', 'precio_compra', 'stock_actual')
            ->get();

        return view('compras.create', compact('proveedores', 'productos'));
    }

    /**
     * Procesar la compra en transacción atómica:
     * 1. Inserta la compra cabecera
     * 2. Inserta los detalles de compra
     * 3. Sube el stock actual de cada producto
     * 4. Registra cada ingreso en movimientos_inventario (Kardex)
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'proveedor_id'       => ['required', 'integer', 'exists:proveedores,id'],
            'tipo_comprobante'   => ['required', 'string', 'max:30'],
            'numero_comprobante' => ['nullable', 'string', 'max:50'],
            'fecha_compra'       => ['required', 'date'],
            'descuento'          => ['nullable', 'numeric', 'min:0'],
            'impuesto'           => ['nullable', 'numeric', 'min:0'],
            'observaciones'      => ['nullable', 'string', 'max:500'],
            'detalles'           => ['required', 'array', 'min:1'],
            'detalles.*.producto_id'    => ['required', 'integer', 'exists:productos,id'],
            'detalles.*.cantidad'       => ['required', 'numeric', 'gt:0'],
            'detalles.*.costo_unitario' => ['required', 'numeric', 'min:0'],
        ], [
            'detalles.required' => 'Debes agregar al menos un producto a la compra.',
            'detalles.min'      => 'Debes agregar al menos un producto a la compra.',
        ]);

        $compraId = DB::transaction(function () use ($datos) {
            $subtotal = 0;
            $itemsValidados = [];

            foreach ($datos['detalles'] as $item) {
                $sub = round($item['cantidad'] * $item['costo_unitario'], 2);
                $subtotal += $sub;
                $itemsValidados[] = [
                    'producto_id'    => (int) $item['producto_id'],
                    'cantidad'       => (float) $item['cantidad'],
                    'costo_unitario' => (float) $item['costo_unitario'],
                    'subtotal'       => $sub,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            $descuento = (float) ($datos['descuento'] ?? 0);
            $impuesto  = (float) ($datos['impuesto'] ?? 0);
            $total     = max(0, round($subtotal - $descuento + $impuesto, 2));

            // 1. Cabecera de compra
            $compraId = DB::table('compras')->insertGetId([
                'proveedor_id'       => $datos['proveedor_id'],
                'usuario_id'         => auth()->id(),
                'tipo_comprobante'   => $datos['tipo_comprobante'],
                'numero_comprobante' => $datos['numero_comprobante'] ?: ('COMP-' . strtoupper(uniqid())),
                'fecha_compra'       => $datos['fecha_compra'],
                'subtotal'           => $subtotal,
                'impuesto'           => $impuesto,
                'descuento'          => $descuento,
                'total'              => $total,
                'estado'             => 'recibida',
                'observaciones'      => $datos['observaciones'] ?? null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // 2. Insertar detalles y actualizar stock + kardex
            foreach ($itemsValidados as $item) {
                $item['compra_id'] = $compraId;
                DB::table('detalle_compras')->insert($item);

                // Bloqueo y lectura del stock anterior
                $producto = DB::table('productos')
                    ->where('id', $item['producto_id'])
                    ->lockForUpdate()
                    ->first();

                $stockAnterior = (float) $producto->stock_actual;
                $stockNuevo = $stockAnterior + $item['cantidad'];

                // Subir stock y actualizar precio de compra
                DB::table('productos')->where('id', $item['producto_id'])->update([
                    'stock_actual'  => $stockNuevo,
                    'precio_compra' => $item['costo_unitario'],
                    'updated_at'    => now(),
                ]);

                // Registrar en kardex (movimientos_inventario)
                DB::table('movimientos_inventario')->insert([
                    'producto_id'      => $item['producto_id'],
                    'usuario_id'       => auth()->id(),
                    'compra_id'        => $compraId,
                    'venta_id'         => null,
                    'tipo'             => 'entrada',
                    'cantidad'         => $item['cantidad'],
                    'stock_anterior'   => $stockAnterior,
                    'stock_nuevo'      => $stockNuevo,
                    'motivo'           => "Ingreso por compra {$datos['tipo_comprobante']} " . ($datos['numero_comprobante'] ?? "#{$compraId}"),
                    'fecha_movimiento' => $datos['fecha_compra'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            return $compraId;
        });

        return redirect()->route('compras.show', $compraId)
            ->with('ok', 'Compra registrada con éxito y existencias de almacén actualizadas.');
    }

    /**
     * Ver comprobante y detalle de una compra.
     */
    public function show(int $id)
    {
        $compra = DB::table('compras')
            ->join('proveedores', 'proveedores.id', '=', 'compras.proveedor_id')
            ->join('usuarios', 'usuarios.id', '=', 'compras.usuario_id')
            ->select(
                'compras.*',
                'proveedores.razon_social as proveedor_nombre',
                'proveedores.tipo_documento as proveedor_tipo_doc',
                'proveedores.numero_documento as proveedor_doc',
                'proveedores.telefono as proveedor_tel',
                'proveedores.direccion as proveedor_dir',
                DB::raw("CONCAT(usuarios.nombres, ' ', usuarios.apellidos) as usuario_nombre")
            )
            ->where('compras.id', $id)
            ->first() ?? abort(404, 'Compra no encontrada.');

        $detalles = DB::table('detalle_compras')
            ->join('productos', 'productos.id', '=', 'detalle_compras.producto_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->select(
                'detalle_compras.*',
                'productos.nombre as producto_nombre',
                'productos.codigo as producto_codigo',
                'unidades_medida.abreviatura as unidad'
            )
            ->where('detalle_compras.compra_id', $id)
            ->get();

        return view('compras.show', compact('compra', 'detalles'));
    }

    /**
     * Anular una compra (Solo Administrador).
     * Revierte el stock en productos y deja registrado el ajuste en inventario.
     */
    public function anular(int $id)
    {
        if (auth()->user()->rol?->nombre !== 'Administrador') {
            abort(403, 'Solo el Administrador tiene autorización para anular compras.');
        }

        $compra = DB::table('compras')->where('id', $id)->first() ?? abort(404);

        if ($compra->estado === 'anulada') {
            return redirect()->route('compras.show', $id)
                ->with('error', 'Esta compra ya se encuentra anulada.');
        }

        DB::transaction(function () use ($compra, $id) {
            $detalles = DB::table('detalle_compras')->where('compra_id', $id)->get();

            foreach ($detalles as $det) {
                $producto = DB::table('productos')->where('id', $det->producto_id)->lockForUpdate()->first();
                $stockAnterior = (float) $producto->stock_actual;
                $stockNuevo = max(0, $stockAnterior - (float) $det->cantidad);

                DB::table('productos')->where('id', $det->producto_id)->update([
                    'stock_actual' => $stockNuevo,
                    'updated_at'   => now(),
                ]);

                DB::table('movimientos_inventario')->insert([
                    'producto_id'      => $det->producto_id,
                    'usuario_id'       => auth()->id(),
                    'compra_id'        => $id,
                    'venta_id'         => null,
                    'tipo'             => 'ajuste_negativo',
                    'cantidad'         => $det->cantidad,
                    'stock_anterior'   => $stockAnterior,
                    'stock_nuevo'      => $stockNuevo,
                    'motivo'           => "Anulación de compra #{$id} ({$compra->numero_comprobante})",
                    'fecha_movimiento' => now(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            DB::table('compras')->where('id', $id)->update([
                'estado'     => 'anulada',
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('compras.show', $id)
            ->with('ok', 'Compra anulada exitosamente y stock revertido en inventario.');
    }
}
