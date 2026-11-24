@extends('layouts.panel')

@section('titulo', 'Reportes')
@section('encabezado', 'Reportes y Métricas')
@section('subtitulo', 'Análisis comercial de ventas, métodos de pago, productos estrella y alertas de stock.')

@push('styles')
    @include('partials.ui')
    <style>
        .reporte-seccion { margin-bottom: 28px; }
        .grid-reportes { display: grid; grid-template-columns: 1.4fr 1fr; gap: 20px; }
        @media (max-width: 900px) { .grid-reportes { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('contenido')
    <!-- Filtro de Rango de Fechas -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" style="display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end;">
                <div class="campo" style="margin:0; min-width:180px;">
                    <label>Fecha inicial</label>
                    <input type="date" name="desde" value="{{ $fechaDesde }}" required>
                </div>

                <div class="campo" style="margin:0; min-width:180px;">
                    <label>Fecha final</label>
                    <input type="date" name="hasta" value="{{ $fechaHasta }}" required>
                </div>

                <div style="display:flex; gap:8px;">
                    <button class="btn btn-rosa" type="submit">Generar reporte</button>
                    <a class="btn btn-claro" href="{{ route('reportes.index', ['desde' => now()->startOfMonth()->toDateString(), 'hasta' => now()->toDateString()]) }}">Este mes</a>
                    <a class="btn btn-claro" href="{{ route('reportes.index', ['desde' => now()->toDateString(), 'hasta' => now()->toDateString()]) }}">Hoy</a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs del período -->
    <div class="kpis tres">
        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Total Recaudado</span>
                <div class="kpi-icon rosa">💵</div>
            </div>
            <div class="kpi-value" style="color:var(--rosa-oscuro);">
                S/ {{ number_format((float) $resumenVentas->monto_total, 2) }}
            </div>
            <div class="kpi-note">En el período seleccionado</div>
        </div>

        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Ventas Cobradas</span>
                <div class="kpi-icon azul">🧾</div>
            </div>
            <div class="kpi-value">
                {{ number_format((int) $resumenVentas->total_transacciones) }}
            </div>
            <div class="kpi-note">Transacciones pagadas</div>
        </div>

        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Ticket Promedio</span>
                <div class="kpi-icon verde">📊</div>
            </div>
            <div class="kpi-value">
                S/ {{ number_format((float) $resumenVentas->ticket_promedio, 2) }}
            </div>
            <div class="kpi-note">Gasto medio por cliente</div>
        </div>
    </div>

    <div class="grid-reportes">
        <!-- Columna 1: Top 10 Productos Más Vendidos -->
        <div class="card">
            <div class="card-head">
                <h2>🏆 Top 10 Productos Más Vendidos</h2>
                <span>Por volumen de venta</span>
            </div>
            @if ($topProductos->count())
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th style="text-align:right;">Cantidad vendida</th>
                                <th style="text-align:right;">Ingreso total</th>
                                <th style="text-align:right;">Stock restante</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topProductos as $idx => $prod)
                                <tr>
                                    <td><strong>{{ $idx + 1 }}</strong></td>
                                    <td>
                                        <strong>{{ $prod->nombre }}</strong>
                                        <span class="sub">[{{ $prod->codigo }}]</span>
                                    </td>
                                    <td style="text-align:right;">
                                        <strong>{{ number_format($prod->total_vendido, 2) }}</strong> {{ $prod->unidad }}
                                    </td>
                                    <td style="text-align:right; color:var(--rosa-oscuro);">
                                        <strong>S/ {{ number_format($prod->total_ingreso, 2) }}</strong>
                                    </td>
                                    <td style="text-align:right;">
                                        {{ number_format($prod->stock_actual, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <p>No hay ventas registradas en el rango de fechas seleccionado.</p>
                </div>
            @endif
        </div>

        <!-- Columna 2: Ventas por Método de Pago -->
        <div class="card">
            <div class="card-head">
                <h2>💳 Ventas por Método de Pago</h2>
                <span>Formas de cobro</span>
            </div>
            @if ($ventasPorMetodo->count())
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th style="text-align:right;">Operaciones</th>
                                <th style="text-align:right;">Monto total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $granTotalMetodos = $ventasPorMetodo->sum('total_monto'); @endphp
                            @foreach ($ventasPorMetodo as $vp)
                                @php $pct = $granTotalMetodos > 0 ? ($vp->total_monto / $granTotalMetodos) * 100 : 0; @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $vp->metodo }}</strong>
                                        <span class="sub">{{ number_format($pct, 1) }}% de las ventas</span>
                                    </td>
                                    <td style="text-align:right;">{{ $vp->cantidad_operaciones }}</td>
                                    <td style="text-align:right;">
                                        <strong>S/ {{ number_format($vp->total_monto, 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <p>No hay cobros registrados en el rango de fechas.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Alertas de Stock Bajo Crítico -->
    <div class="card" style="margin-top:24px;">
        <div class="card-head">
            <h2>⚠️ Alertas de Reposición Urgente (Stock Bajo o Agotado)</h2>
            <a class="btn btn-claro btn-sm" href="{{ route('inventario.index', ['filtro_stock' => 'bajo']) }}">Ver inventario completo</a>
        </div>
        @if ($stockBajo->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th style="text-align:right;">Stock actual</th>
                            <th style="text-align:right;">Stock mínimo</th>
                            <th>Estado de stock</th>
                            <th style="text-align:right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockBajo as $sb)
                            <tr>
                                <td><strong>{{ $sb->codigo }}</strong></td>
                                <td>{{ $sb->nombre }}</td>
                                <td>{{ $sb->categoria_nombre ?? '—' }}</td>
                                <td style="text-align:right;">
                                    <strong style="color: {{ $sb->stock_actual <= 0 ? '#b91c1c' : '#b7791f' }};">
                                        {{ number_format($sb->stock_actual, 2) }} {{ $sb->unidad }}
                                    </strong>
                                </td>
                                <td style="text-align:right;">{{ number_format($sb->stock_minimo, 2) }}</td>
                                <td>
                                    @if ($sb->stock_actual <= 0)
                                        <span class="badge off" style="background:#fee2e2; color:#b91c1c; font-weight:700;">AGOTADO</span>
                                    @else
                                        <span class="badge off" style="background:#fef3c7; color:#92400e; font-weight:700;">BAJO</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <a class="btn btn-rosa btn-sm" href="{{ route('compras.create') }}">Comprar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <p>✓ ¡Excelente! No tienes productos con stock bajo ni agotados.</p>
            </div>
        @endif
    </div>
@endsection
