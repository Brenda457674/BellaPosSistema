@extends('layouts.panel')

@section('titulo', 'Panel de almacén')
@section('encabezado', 'Hola, ' . ($usuario->nombres ?? 'Almacenero'))
@section('subtitulo', 'Estado de existencias, compras y almacén.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    @php
        $totalProductos = DB::table('productos')->where('estado', 'activo')->count();
        $stockBajo = DB::table('productos')->where('estado', 'activo')->whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $comprasMes = DB::table('compras')->where('estado', 'recibida')->whereMonth('fecha_compra', now()->month)->whereYear('fecha_compra', now()->year)->count();

        $movimientosRecientes = DB::table('movimientos_inventario')
            ->join('productos', 'productos.id', '=', 'movimientos_inventario.producto_id')
            ->leftJoin('unidades_medida', 'unidades_medida.id', '=', 'productos.unidad_medida_id')
            ->select(
                'movimientos_inventario.*',
                'productos.nombre as producto_nombre',
                'productos.codigo as producto_codigo',
                'unidades_medida.abreviatura as unidad'
            )
            ->orderByDesc('movimientos_inventario.fecha_movimiento')
            ->orderByDesc('movimientos_inventario.id')
            ->limit(6)
            ->get();
    @endphp

    <div class="kpis tres">
        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Productos en almacén</span>
                <span class="kpi-icon azul">📦</span>
            </div>
            <div class="kpi-value">{{ $totalProductos }}</div>
            <div class="kpi-note">En catálogo activo</div>
        </article>

        <article class="kpi" style="{{ $stockBajo > 0 ? 'border-color:#fca5a5;' : '' }}">
            <div class="kpi-top">
                <span class="kpi-label" style="{{ $stockBajo > 0 ? 'color:#b91c1c; font-weight:700;' : '' }}">Stock bajo / Alerta</span>
                <span class="kpi-icon ambar" style="{{ $stockBajo > 0 ? 'background:#fee2e2; color:#b91c1c;' : '' }}">⚠️</span>
            </div>
            <div class="kpi-value" style="{{ $stockBajo > 0 ? 'color:#b91c1c;' : '' }}">{{ $stockBajo }}</div>
            <div class="kpi-note">Por debajo del stock mínimo</div>
        </article>

        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Compras del mes</span>
                <span class="kpi-icon rosa">🛒</span>
            </div>
            <div class="kpi-value">{{ $comprasMes }}</div>
            <div class="kpi-note">Ingresos de mercadería este mes</div>
        </article>
    </div>

    <!-- Movimientos Recientes de Almacén -->
    <section class="card" style="margin-bottom:24px">
        <div class="card-head">
            <h2>Movimientos recientes de inventario</h2>
            <a class="btn btn-claro btn-sm" href="{{ route('inventario.kardex') }}">Ver Kardex completo</a>
        </div>

        @if ($movimientosRecientes->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th style="text-align:right;">Cantidad</th>
                            <th style="text-align:right;">Stock resultante</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movimientosRecientes as $mov)
                            @php
                                $esPositivo = in_array($mov->tipo, ['entrada', 'ajuste_positivo', 'devolucion'], true);
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($mov->fecha_movimiento)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <strong>{{ $mov->producto_nombre }}</strong>
                                    <span class="sub">[{{ $mov->producto_codigo }}]</span>
                                </td>
                                <td>
                                    <span class="badge" style="font-weight:700; background:{{ $esPositivo ? '#dcfce7' : '#fee2e2' }}; color:{{ $esPositivo ? '#166534' : '#991b1b' }};">
                                        {{ ucfirst(str_replace('_', ' ', $mov->tipo)) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <strong style="color:{{ $esPositivo ? 'var(--verde)' : 'var(--rosa-oscuro)' }};">
                                        {{ $esPositivo ? '+' : '-' }}{{ number_format($mov->cantidad, 2) }}
                                    </strong>
                                    <span class="sub">{{ $mov->unidad ?? '' }}</span>
                                </td>
                                <td style="text-align:right;"><strong>{{ number_format($mov->stock_nuevo, 2) }}</strong></td>
                                <td>{{ $mov->motivo ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">📊</div>
                <h3>Sin movimientos de almacén</h3>
                <p>Las entradas y salidas de productos aparecerán automáticamente aquí.</p>
            </div>
        @endif
    </section>

    <!-- Accesos Rápidos del Almacenero -->
    <h2 class="section-title">Accesos rápidos</h2>
    <div class="actions">
        <a class="action" href="{{ route('compras.create') }}">
            <span class="ic">🛒</span>
            <span>Registrar compra<small>Ingreso de mercadería</small></span>
        </a>
        <a class="action" href="{{ route('inventario.index') }}">
            <span class="ic">📋</span>
            <span>Control de stock<small>Existencias y alertas</small></span>
        </a>
        <a class="action" href="{{ route('inventario.ajuste') }}">
            <span class="ic">⚙️</span>
            <span>Ajustar stock<small>Corrección de almacén</small></span>
        </a>
        <a class="action" href="{{ route('admin.productos.index') }}">
            <span class="ic">📦</span>
            <span>Productos<small>Catálogo y unidades</small></span>
        </a>
        <a class="action" href="{{ route('proveedores.index') }}">
            <span class="ic">🚚</span>
            <span>Proveedores<small>Directorio de compras</small></span>
        </a>
    </div>
@endsection
