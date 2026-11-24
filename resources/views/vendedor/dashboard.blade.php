@extends('layouts.panel')

@section('titulo', 'Panel de ventas')
@section('encabezado', 'Hola, ' . ($usuario->nombres ?? 'Vendedor'))
@section('subtitulo', 'Tu actividad de ventas y recaudación de hoy.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    @if (session('status') === 'Correo verificado.')
        <div style="background:#e8f8ee; color:#176b3a; border:1px solid #b7e4c7; padding:16px 20px; border-radius:12px; margin-bottom:24px;">
            <strong>✓ ¡Correo verificado correctamente!</strong>
            <p style="margin:6px 0 0;">Tu cuenta de Bella Flor POS ya está verificada. Bienvenida/o, {{ $usuario->nombres }}.</p>
        </div>
    @endif

    @php
        $idUsuario = $usuario->id;
        $vendidoHoy = DB::table('ventas')->where('usuario_id', $idUsuario)->where('estado', 'pagada')->whereDate('fecha_venta', today())->sum('total');
        $cantVentasHoy = DB::table('ventas')->where('usuario_id', $idUsuario)->where('estado', 'pagada')->whereDate('fecha_venta', today())->count();
        $clientesHoy = DB::table('ventas')->where('usuario_id', $idUsuario)->where('estado', 'pagada')->whereDate('fecha_venta', today())->distinct('cliente_id')->count();

        $misVentasRecientes = DB::table('ventas')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->select(
                'ventas.*',
                DB::raw("COALESCE(CONCAT(clientes.nombres, ' ', COALESCE(clientes.apellidos, '')), 'Público General') as cliente_nombre")
            )
            ->where('ventas.usuario_id', $idUsuario)
            ->orderByDesc('ventas.fecha_venta')
            ->limit(5)
            ->get();
    @endphp

    <div class="kpis tres">
        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Vendido hoy</span>
                <span class="kpi-icon rosa">💵</span>
            </div>
            <div class="kpi-value" style="color:var(--rosa-oscuro);">S/ {{ number_format((float) $vendidoHoy, 2) }}</div>
            <div class="kpi-note">Total cobrado por tu usuario hoy</div>
        </article>

        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Ventas realizadas</span>
                <span class="kpi-icon azul">🧾</span>
            </div>
            <div class="kpi-value">{{ $cantVentasHoy }}</div>
            <div class="kpi-note">Boletas emitidas hoy</div>
        </article>

        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Clientes atendidos</span>
                <span class="kpi-icon verde">👥</span>
            </div>
            <div class="kpi-value">{{ $clientesHoy }}</div>
            <div class="kpi-note">En el turno de hoy</div>
        </article>
    </div>

    <!-- Mis Ventas Recientes -->
    <section class="card" style="margin-bottom:24px">
        <div class="card-head">
            <h2>Mis ventas recientes</h2>
            <a class="btn btn-claro btn-sm" href="{{ route('pos.index') }}">Ver todas mis ventas</a>
        </div>

        @if ($misVentasRecientes->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th style="text-align:right;">Monto</th>
                            <th style="text-align:right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($misVentasRecientes as $mv)
                            <tr>
                                <td><strong>{{ $mv->serie }}-{{ $mv->numero }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($mv->fecha_venta)->format('d/m/Y H:i') }}</td>
                                <td>{{ $mv->cliente_nombre }}</td>
                                <td style="text-align:right;"><strong>S/ {{ number_format($mv->total, 2) }}</strong></td>
                                <td style="text-align:right;">
                                    <a class="btn btn-claro btn-sm" href="{{ route('pos.show', $mv->id) }}">Ver ticket</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">🧾</div>
                <h3>Aún no has registrado ventas hoy</h3>
                <p>Haz clic en "Nueva venta" para abrir el Punto de Venta y empezar a cobrar.</p>
                <a class="btn btn-rosa" href="{{ route('pos.create') }}">+ Nueva venta</a>
            </div>
        @endif
    </section>

    <!-- Accesos Rápidos del Vendedor -->
    <h2 class="section-title">Accesos rápidos</h2>
    <div class="actions">
        <a class="action" href="{{ route('pos.create') }}">
            <span class="ic">💳</span>
            <span>Nueva venta<small>Punto de venta (POS)</small></span>
        </a>
        <a class="action" href="{{ route('pos.index') }}">
            <span class="ic">🧾</span>
            <span>Mis ventas<small>Historial de cobros</small></span>
        </a>
        <a class="action" href="{{ route('clientes.index') }}">
            <span class="ic">👥</span>
            <span>Clientes<small>Directorio de clientes</small></span>
        </a>
        <a class="action" href="{{ route('admin.productos.index') }}">
            <span class="ic">📦</span>
            <span>Consultar productos<small>Precios y stock</small></span>
        </a>
    </div>
@endsection
