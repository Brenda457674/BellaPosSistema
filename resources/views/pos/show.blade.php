@extends('layouts.panel')

@section('titulo', 'Boleta ' . $venta->serie . '-' . $venta->numero)
@section('encabezado', 'Comprobante de Venta')
@section('subtitulo', 'Boleta Electrónica ' . $venta->serie . '-' . $venta->numero . ' · Emitida el ' . \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i:s'))

@push('styles')
    @include('partials.ui')
    <style>
        .vista-comprobante { display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: start; }
        .ticket-box {
            background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 28px 24px;
            max-width: 420px; width: 100%; box-shadow: var(--sombra); margin: 0 auto; font-family: "Courier New", Courier, monospace, monospace;
        }
        .ticket-head { text-align: center; border-bottom: 1px dashed var(--borde); padding-bottom: 16px; margin-bottom: 16px; }
        .ticket-logo { font-size: 26px; margin-bottom: 4px; }
        .ticket-empresa { font-size: 17px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .ticket-sub { font-size: 11.5px; color: var(--texto-2); line-height: 1.4; }
        .ticket-doc { font-size: 14px; font-weight: 700; margin: 10px 0 2px; }
        .ticket-fila { display: flex; justify-content: space-between; font-size: 12px; padding: 3px 0; }
        .ticket-items { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 12px; }
        .ticket-items th { font-size: 11px; padding: 6px 2px; border-bottom: 1px dashed #000; text-transform: uppercase; background: none; }
        .ticket-items td { padding: 6px 2px; border-bottom: 1px dotted #e5e5e5; }
        .ticket-totales { border-top: 1px dashed #000; padding-top: 8px; margin-top: 6px; }
        .ticket-gran-total { font-size: 15px; font-weight: 700; display: flex; justify-content: space-between; padding-top: 4px; }
        .ticket-foot { text-align: center; font-size: 11px; color: var(--texto-2); border-top: 1px dashed var(--borde); padding-top: 12px; margin-top: 16px; }

        @media (max-width: 800px) { .vista-comprobante { grid-template-columns: 1fr; } }

        /* Estilos de Impresión */
        @media print {
            body * { visibility: hidden; }
            .ticket-box, .ticket-box * { visibility: visible; }
            .ticket-box {
                position: absolute; left: 0; top: 0; width: 80mm; max-width: 80mm;
                border: none; box-shadow: none; padding: 4mm; margin: 0;
            }
            .sidebar, .topbar, .barra-acciones, .footer, .btn { display: none !important; }
        }
    </style>
@endpush

@section('contenido')
    <div class="vista-comprobante">
        <!-- Ticket Físico / Digital -->
        <div class="ticket-box">
            <div class="ticket-head">
                <div class="ticket-logo">🌸</div>
                <div class="ticket-empresa">{{ $tienda->nombre ?? 'Bella Flor POS' }}</div>
                @if ($tienda->ruc)<div class="ticket-sub">RUC: {{ $tienda->ruc }}</div>@endif
                @if ($tienda->direccion)<div class="ticket-sub">{{ $tienda->direccion }}</div>@endif
                @if ($tienda->telefono)<div class="ticket-sub">Tel: {{ $tienda->telefono }}</div>@endif

                <div class="ticket-doc">BOLETA DE VENTA</div>
                <div style="font-size:13px; font-weight:700;">{{ $venta->serie }}-{{ $venta->numero }}</div>
                <div class="ticket-sub">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i:s') }}</div>

                @if ($venta->estado === 'anulada')
                    <div style="margin-top:8px; padding:4px; background:#fee2e2; color:#b91c1c; font-weight:700; font-size:12px; border:1px solid #f87171;">
                        *** VENTA ANULADA ***
                    </div>
                @endif
            </div>

            <div class="ticket-fila">
                <span>Cliente:</span>
                <strong>{{ $venta->cliente_nombre }}</strong>
            </div>
            @if ($venta->cliente_doc)
                <div class="ticket-fila">
                    <span>Documento:</span>
                    <span>{{ $venta->cliente_tipo_doc }}: {{ $venta->cliente_doc }}</span>
                </div>
            @endif
            <div class="ticket-fila">
                <span>Atendido por:</span>
                <span>{{ $venta->vendedor_nombre }}</span>
            </div>

            <!-- Tabla de Ítems -->
            <table class="ticket-items">
                <thead>
                    <tr>
                        <th style="text-align:left;">Cant/Prod</th>
                        <th style="text-align:right;">P.Unit</th>
                        <th style="text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalles as $d)
                        <tr>
                            <td>
                                <div>{{ $d->producto_nombre }}</div>
                                <span style="font-size:10.5px; color:var(--texto-2);">{{ number_format($d->cantidad, 2) }} {{ $d->unidad }}</span>
                            </td>
                            <td style="text-align:right;">{{ number_format($d->precio_unitario, 2) }}</td>
                            <td style="text-align:right;"><strong>{{ number_format($d->subtotal, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totales del Ticket -->
            <div class="ticket-totales">
                <div class="ticket-fila">
                    <span>Op. Gravada / Subtotal:</span>
                    <span>S/ {{ number_format($venta->subtotal, 2) }}</span>
                </div>
                <div class="ticket-fila">
                    <span>IGV (0.00%):</span>
                    <span>S/ 0.00</span>
                </div>
                <div class="ticket-gran-total">
                    <span>TOTAL A PAGAR:</span>
                    <span>S/ {{ number_format($venta->total, 2) }}</span>
                </div>

                @if ($pago)
                    <div class="ticket-fila" style="margin-top:8px; border-top:1px dotted #ccc; padding-top:6px;">
                        <span>Forma de pago:</span>
                        <strong>{{ $pago->metodo_nombre }}</strong>
                    </div>
                    @if ($pago->referencia)
                        <div class="ticket-fila">
                            <span>N° Operación / Ref:</span>
                            <span>{{ $pago->referencia }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <div class="ticket-foot">
                <div>{{ $tienda->mensaje_comprobante ?? '¡Gracias por tu compra en Bella Flor!' }}</div>
                <div style="margin-top:4px;">Conserve este comprobante para cualquier cambio.</div>
            </div>
        </div>

        <!-- Columna de Acciones de la Venta -->
        <div class="barra-acciones" style="display:flex; flex-direction:column; gap:12px; min-width:240px;">
            <button type="button" class="btn btn-rosa" onclick="window.print()" style="padding:12px; font-size:14px; justify-content:center;">
                🖨️ Imprimir ticket
            </button>

            <a class="btn btn-claro" href="{{ route('pos.create') }}" style="justify-content:center;">
                + Nueva venta
            </a>

            <a class="btn btn-claro" href="{{ route('pos.index') }}" style="justify-content:center;">
                ← Volver al historial
            </a>

            @if ($esAdmin && $venta->estado === 'pagada')
                <div style="border-top:1px solid var(--borde); padding-top:12px; margin-top:8px;">
                    <form method="POST" action="{{ route('pos.anular', $venta->id) }}" onsubmit="return confirm('¿Seguro que deseas ANULAR esta venta? Los productos regresarán automáticamente al stock.')">
                        @csrf
                        <button type="submit" class="btn btn-rojo" style="width:100%; justify-content:center;">
                            🚫 Anular esta venta
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
