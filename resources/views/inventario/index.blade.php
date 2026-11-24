@extends('layouts.panel')

@section('titulo', 'Inventario')
@section('encabezado', 'Control de Inventario')
@section('subtitulo', 'Monitorea existencias, alertas de reposición y valor de la mercadería.')

@push('styles')
    @include('partials.ui')
    <style>
        .kpi-link { text-decoration: none; color: inherit; display: block; }
        .kpi-link:hover .kpi { border-color: var(--rosa-borde); box-shadow: 0 4px 12px rgba(214, 90, 122, .12); }
    </style>
@endpush

@section('contenido')
    <!-- KPIs de Inventario -->
    <div class="kpis">
        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Productos Activos</span>
                <div class="kpi-icon azul">📦</div>
            </div>
            <div class="kpi-value">{{ number_format($kpis['total_productos']) }}</div>
            <div class="kpi-note">En catálogo de tienda</div>
        </div>

        <a class="kpi-link" href="{{ route('inventario.index', ['filtro_stock' => 'bajo']) }}">
            <div class="kpi" style="{{ $kpis['stock_bajo'] > 0 ? 'border-color:#fca5a5; background:#fffdfd;' : '' }}">
                <div class="kpi-top">
                    <span class="kpi-label" style="{{ $kpis['stock_bajo'] > 0 ? 'color:#b91c1c; font-weight:700;' : '' }}">Stock Bajo</span>
                    <div class="kpi-icon ambar" style="{{ $kpis['stock_bajo'] > 0 ? 'background:#fee2e2; color:#b91c1c;' : '' }}">⚠️</div>
                </div>
                <div class="kpi-value" style="{{ $kpis['stock_bajo'] > 0 ? 'color:#b91c1c;' : '' }}">{{ number_format($kpis['stock_bajo']) }}</div>
                <div class="kpi-note">Por debajo del mínimo</div>
            </div>
        </a>

        <a class="kpi-link" href="{{ route('inventario.index', ['filtro_stock' => 'agotado']) }}">
            <div class="kpi" style="{{ $kpis['agotados'] > 0 ? 'border-color:#fca5a5; background:#fffdfd;' : '' }}">
                <div class="kpi-top">
                    <span class="kpi-label" style="{{ $kpis['agotados'] > 0 ? 'color:#b91c1c; font-weight:700;' : '' }}">Agotados</span>
                    <div class="kpi-icon rosa">🚫</div>
                </div>
                <div class="kpi-value" style="{{ $kpis['agotados'] > 0 ? 'color:#b91c1c;' : '' }}">{{ number_format($kpis['agotados']) }}</div>
                <div class="kpi-note">Existencia en 0</div>
            </div>
        </a>

        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Valor del Inventario</span>
                <div class="kpi-icon verde">💰</div>
            </div>
            <div class="kpi-value" style="color:var(--verde);">S/ {{ number_format($kpis['valor_total'], 2) }}</div>
            <div class="kpi-note">A costo de compra</div>
        </div>
    </div>

    <!-- Barra de acciones y filtros -->
    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('inventario.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por producto, código o barra...">

            <select name="categoria_id" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id }}" @selected($categoriaId == $cat->id)>{{ $cat->nombre }}</option>
                @endforeach
            </select>

            <select name="filtro_stock" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="todos" @selected($filtroStock === 'todos')>Todos los niveles de stock</option>
                <option value="bajo" @selected($filtroStock === 'bajo')>⚠️ Stock bajo (≤ mínimo)</option>
                <option value="agotado" @selected($filtroStock === 'agotado')>🚫 Agotados (≤ 0)</option>
            </select>

            <button class="btn btn-claro" type="submit">Filtrar</button>
            @if ($q || $categoriaId || $filtroStock !== 'todos')
                <a class="btn btn-claro" href="{{ route('inventario.index') }}">Limpiar</a>
            @endif
        </form>

        <div style="display:flex; gap:8px;">
            <a class="btn btn-claro" href="{{ route('inventario.kardex') }}">📊 Ver Kardex</a>
            <a class="btn btn-rosa" href="{{ route('inventario.ajuste') }}">⚙️ Ajustar stock</a>
        </div>
    </div>

    <!-- Tabla de existencias -->
    <div class="card">
        @if ($productos->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Stock actual</th>
                            <th>Stock mínimo</th>
                            <th>Costo / Venta</th>
                            <th>Estado stock</th>
                            <th style="text-align:right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $p)
                            @php
                                $esAgotado = $p->stock_actual <= 0;
                                $esBajo = !$esAgotado && ($p->stock_actual <= $p->stock_minimo);
                            @endphp
                            <tr style="{{ $esAgotado ? 'background:#fff9f9;' : ($esBajo ? 'background:#fffdfa;' : '') }}">
                                <td>
                                    <strong>{{ $p->codigo }}</strong>
                                    @if ($p->codigo_barras)
                                        <span class="sub">🏷️ {{ $p->codigo_barras }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $p->nombre }}</strong>
                                    @if ($p->marca)<span class="sub">{{ $p->marca }}</span>@endif
                                </td>
                                <td>{{ $p->categoria_nombre ?? '—' }}</td>
                                <td>
                                    <strong style="font-size:15px; color: {{ $esAgotado ? '#b91c1c' : ($esBajo ? '#b7791f' : 'var(--texto)') }};">
                                        {{ number_format($p->stock_actual, 2) }}
                                    </strong>
                                    <span class="sub">{{ $p->unidad_abrev ?? 'UND' }}</span>
                                </td>
                                <td>
                                    {{ number_format($p->stock_minimo, 2) }}
                                    <span class="sub">{{ $p->unidad_abrev ?? 'UND' }}</span>
                                </td>
                                <td>
                                    <div>Compra: S/ {{ number_format($p->precio_compra, 2) }}</div>
                                    <div class="sub">Venta: S/ {{ number_format($p->precio_venta, 2) }}</div>
                                </td>
                                <td>
                                    @if ($esAgotado)
                                        <span class="badge off" style="background:#fee2e2; color:#b91c1c; font-weight:700;">
                                            🚫 AGOTADO
                                        </span>
                                    @elseif ($esBajo)
                                        <span class="badge off" style="background:#fef3c7; color:#92400e; font-weight:700;">
                                            ⚠️ STOCK BAJO
                                        </span>
                                    @else
                                        <span class="badge ok">
                                            ✓ Normal
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <a class="btn btn-claro btn-sm" href="{{ route('inventario.ajuste', ['producto_id' => $p->id]) }}" title="Ajustar stock">
                                        Ajustar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="paginacion">
                {{ $productos->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <h3>No hay productos que coincidan con el filtro</h3>
                <p>Prueba limpiando los filtros para ver la lista completa de existencias.</p>
                <a class="btn btn-claro" href="{{ route('inventario.index') }}">Ver todo el inventario</a>
            </div>
        @endif
    </div>
@endsection
