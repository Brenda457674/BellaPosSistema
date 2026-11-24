@extends('layouts.panel')

@section('titulo', 'Compra #' . $compra->id)
@section('encabezado', 'Comprobante de Compra #' . $compra->id)
@section('subtitulo', $compra->tipo_comprobante . ' ' . $compra->numero_comprobante . ' · Registrada el ' . \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y H:i'))

@push('styles')
    @include('partials.ui')
    <style>
        .resumen-compra { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
        .dato-fila { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--borde); font-size: 13.5px; }
        .dato-label { color: var(--texto-2); }
        .dato-val { font-weight: 600; text-align: right; }
        @media (max-width: 800px) { .resumen-compra { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('contenido')
    @php
        $esAdmin = (auth()->user()->rol?->nombre === 'Administrador');
    @endphp

    <div class="resumen-compra">
        <!-- Tarjeta de Proveedor y Comprobante -->
        <div class="card">
            <div class="card-head">
                <h2>Datos del Proveedor y Comprobante</h2>
                <span class="badge {{ $compra->estado === 'recibida' ? 'ok' : 'off' }}" style="{{ $compra->estado === 'anulada' ? 'background:#fee2e2; color:#b91c1c;' : '' }}">
                    {{ ucfirst($compra->estado) }}
                </span>
            </div>
            <div class="card-body">
                <div class="dato-fila">
                    <span class="dato-label">Proveedor</span>
                    <span class="dato-val">{{ $compra->proveedor_nombre }}</span>
                </div>
                <div class="dato-fila">
                    <span class="dato-label">Documento</span>
                    <span class="dato-val">{{ $compra->proveedor_doc ? $compra->proveedor_tipo_doc . ': ' . $compra->proveedor_doc : '—' }}</span>
                </div>
                <div class="dato-fila">
                    <span class="dato-label">Teléfono / Contacto</span>
                    <span class="dato-val">{{ $compra->proveedor_tel ?: '—' }}</span>
                </div>
                <div class="dato-fila">
                    <span class="dato-label">Tipo y N° de comprobante</span>
                    <span class="dato-val">{{ $compra->tipo_comprobante }}: {{ $compra->numero_comprobante ?: '—' }}</span>
                </div>
                <div class="dato-fila">
                    <span class="dato-label">Registrado en sistema por</span>
                    <span class="dato-val">{{ $compra->usuario_nombre }}</span>
                </div>
                @if ($compra->observaciones)
                    <div class="dato-fila" style="border-bottom:none;">
                        <span class="dato-label">Observaciones</span>
                        <span class="dato-val">{{ $compra->observaciones }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta de Totales y Acciones -->
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="card">
                <div class="card-head">
                    <h2>Totales de la Compra</h2>
                </div>
                <div class="card-body">
                    <div class="dato-fila">
                        <span class="dato-label">Subtotal</span>
                        <span class="dato-val">S/ {{ number_format($compra->subtotal, 2) }}</span>
                    </div>
                    @if ($compra->descuento > 0)
                        <div class="dato-fila">
                            <span class="dato-label">Descuento</span>
                            <span class="dato-val" style="color:var(--verde);">- S/ {{ number_format($compra->descuento, 2) }}</span>
                        </div>
                    @endif
                    @if ($compra->impuesto > 0)
                        <div class="dato-fila">
                            <span class="dato-label">Impuesto / IGV</span>
                            <span class="dato-val">S/ {{ number_format($compra->impuesto, 2) }}</span>
                        </div>
                    @endif
                    <div class="dato-fila" style="border-bottom:none; font-size:16px; padding-top:12px;">
                        <span class="dato-label" style="font-weight:700; color:var(--texto);">TOTAL PAGADO</span>
                        <span class="dato-val" style="color:var(--rosa-oscuro); font-size:18px;">
                            S/ {{ number_format($compra->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a class="btn btn-claro" href="{{ route('compras.index') }}" style="flex:1; justify-content:center;">
                    ← Volver a compras
                </a>

                @if ($esAdmin && $compra->estado === 'recibida')
                    <form method="POST" action="{{ route('compras.anular', $compra->id) }}" onsubmit="return confirm('¿Seguro que deseas ANULAR esta compra? El stock ingresado se restará del inventario.')" style="margin:0;">
                        @csrf
                        <button class="btn btn-rojo" type="submit">🚫 Anular compra</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabla de Productos Comprados -->
    <div class="card">
        <div class="card-head">
            <h2>Productos y cantidades ingresadas a almacén</h2>
            <span>{{ $detalles->count() }} producto(s)</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th style="text-align:right;">Cantidad</th>
                        <th style="text-align:right;">Costo Unitario</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $d)
                        <tr>
                            <td><strong>{{ $d->producto_codigo }}</strong></td>
                            <td>{{ $d->producto_nombre }}</td>
                            <td style="text-align:right;">
                                <strong>{{ number_format($d->cantidad, 2) }}</strong> {{ $d->unidad ?? '' }}
                            </td>
                            <td style="text-align:right;">S/ {{ number_format($d->costo_unitario, 2) }}</td>
                            <td style="text-align:right;"><strong>S/ {{ number_format($d->subtotal, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right;">TOTAL:</th>
                        <th style="text-align:right; font-size:15px; color:var(--rosa-oscuro);">S/ {{ number_format($compra->total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
