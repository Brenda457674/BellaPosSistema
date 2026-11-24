@extends('layouts.panel')

@section('titulo', 'Compras')
@section('encabezado', 'Compras de Mercadería')
@section('subtitulo', 'Ingresos de productos a almacén por compras a proveedores.')

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('compras.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por número o proveedor...">

            <select name="proveedor_id" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">Todos los proveedores</option>
                @foreach ($proveedores as $prov)
                    <option value="{{ $prov->id }}" @selected($proveedorId == $prov->id)>{{ $prov->razon_social }}</option>
                @endforeach
            </select>

            <select name="estado" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">Todos los estados</option>
                <option value="recibida" @selected($estado === 'recibida')>Recibida</option>
                <option value="anulada" @selected($estado === 'anulada')>Anulada</option>
            </select>

            <button class="btn btn-claro" type="submit">Filtrar</button>
            @if ($q || $estado || $proveedorId)
                <a class="btn btn-claro" href="{{ route('compras.index') }}">Limpiar</a>
            @endif
        </form>

        <a class="btn btn-rosa" href="{{ route('compras.create') }}">+ Registrar compra</a>
    </div>

    <div class="card">
        @if ($compras->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Registrado por</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th style="text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->tipo_comprobante }}</strong>
                                    <span class="sub">{{ $c->numero_comprobante ?: '—' }}</span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($c->fecha_compra)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <strong>{{ $c->proveedor_nombre }}</strong>
                                    @if ($c->proveedor_doc)
                                        <span class="sub">Doc: {{ $c->proveedor_doc }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $c->usuario_nombre }}
                                </td>
                                <td>
                                    <strong style="color:var(--texto); font-size:14.5px;">S/ {{ number_format($c->total, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $c->estado === 'recibida' ? 'ok' : 'off' }}" style="{{ $c->estado === 'anulada' ? 'background:#fee2e2; color:#b91c1c;' : '' }}">
                                        {{ ucfirst($c->estado) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <a class="btn btn-claro btn-sm" href="{{ route('compras.show', $c->id) }}">Ver detalle</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="paginacion">
                {{ $compras->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                </div>
                <h3>No hay compras registradas</h3>
                <p>Registra las compras de mercadería para que ingresen al stock de tu inventario.</p>
                <a class="btn btn-rosa" href="{{ route('compras.create') }}">+ Registrar la primera compra</a>
            </div>
        @endif
    </div>
@endsection
