@extends('layouts.panel')

@section('titulo', 'Kardex de Inventario')
@section('encabezado', 'Movimientos de Inventario (Kardex)')
@section('subtitulo', 'Historial detallado de todas las entradas, salidas y ajustes de mercadería.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <!-- Filtros de búsqueda para Kardex -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <form method="GET" action="{{ route('inventario.kardex') }}">
                <div class="form-grid">
                    <div class="campo">
                        <label>Filtrar por producto</label>
                        <select name="producto_id" onchange="this.form.submit()">
                            <option value="">-- Todos los productos --</option>
                            @foreach ($productos as $pr)
                                <option value="{{ $pr->id }}" @selected($productoId == $pr->id)>
                                    [{{ $pr->codigo }}] {{ $pr->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="campo">
                        <label>Tipo de movimiento</label>
                        <select name="tipo" onchange="this.form.submit()">
                            <option value="">-- Todos los tipos --</option>
                            <option value="entrada" @selected($tipo === 'entrada')>Entrada (Compra)</option>
                            <option value="salida" @selected($tipo === 'salida')>Salida (Venta)</option>
                            <option value="ajuste_positivo" @selected($tipo === 'ajuste_positivo')>Ajuste Positivo (+)</option>
                            <option value="ajuste_negativo" @selected($tipo === 'ajuste_negativo')>Ajuste Negativo (-)</option>
                            <option value="devolucion" @selected($tipo === 'devolucion')>Devolución (Venta anulada)</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label>Desde fecha</label>
                        <input type="date" name="fecha_desde" value="{{ $fechaDesde }}">
                    </div>

                    <div class="campo">
                        <label>Hasta fecha</label>
                        <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}">
                    </div>
                </div>

                <div class="form-pie" style="margin-top:14px;">
                    <a class="btn btn-claro" href="{{ route('inventario.index') }}">← Volver a Inventario</a>
                    @if ($productoId || $tipo || $fechaDesde || $fechaHasta)
                        <a class="btn btn-claro" href="{{ route('inventario.kardex') }}">Limpiar filtros</a>
                    @endif
                    <button class="btn btn-rosa" type="submit">Filtrar Kardex</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla Kardex -->
    <div class="card">
        @if ($movimientos->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th style="text-align:right;">Cantidad</th>
                            <th style="text-align:right;">Stock anterior</th>
                            <th style="text-align:right;">Stock nuevo</th>
                            <th>Motivo / Comprobante</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movimientos as $m)
                            @php
                                $esPositivo = in_array($m->tipo, ['entrada', 'ajuste_positivo', 'devolucion'], true);
                                $badgeStyle = match($m->tipo) {
                                    'entrada' => 'background:#dcfce7; color:#166534;',
                                    'devolucion' => 'background:#dbeafe; color:#1e40af;',
                                    'ajuste_positivo' => 'background:#e0e7ff; color:#3730a3;',
                                    'salida' => 'background:#fef2f2; color:#991b1b;',
                                    'ajuste_negativo' => 'background:#fee2e2; color:#991b1b;',
                                    default => ''
                                };
                            @endphp
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($m->fecha_movimiento)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <strong>{{ $m->producto_nombre }}</strong>
                                    <span class="sub">Código: {{ $m->producto_codigo }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="{{ $badgeStyle }} font-weight:700;">
                                        {{ ucfirst(str_replace('_', ' ', $m->tipo)) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <strong style="color: {{ $esPositivo ? 'var(--verde)' : 'var(--rosa-oscuro)' }}; font-size:14.5px;">
                                        {{ $esPositivo ? '+' : '-' }}{{ number_format($m->cantidad, 2) }}
                                    </strong>
                                    <span class="sub">{{ $m->unidad ?? 'UND' }}</span>
                                </td>
                                <td style="text-align:right; color:var(--texto-2);">
                                    {{ number_format($m->stock_anterior, 2) }}
                                </td>
                                <td style="text-align:right;">
                                    <strong>{{ number_format($m->stock_nuevo, 2) }}</strong>
                                </td>
                                <td>
                                    {{ $m->motivo ?: '—' }}
                                </td>
                                <td>
                                    {{ $m->usuario_nombre }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="paginacion">
                {{ $movimientos->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>
                </div>
                <h3>No hay movimientos registrados</h3>
                <p>No se encontraron registros en el Kardex para los filtros seleccionados.</p>
            </div>
        @endif
    </div>
@endsection
