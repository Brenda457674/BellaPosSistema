
@extends('layouts.panel')

@section('titulo', __('messages.products'))
@section('encabezado', __('messages.product_catalog'))
@section('subtitulo', __('messages.product_catalog_subtitle'))

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    @php
        $rol = auth()->user()->rol?->nombre ?? '';
        $puedeEditar = in_array($rol, ['Administrador', 'Almacenero'], true);
        $puedeEliminar = ($rol === 'Administrador');
    @endphp

    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('admin.productos.index') }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_product_placeholder') }}">

            <select name="categoria_id" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">{{ __('messages.all_categories') }}</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id }}" @selected(request('categoria_id') == $cat->id)>{{ $cat->nombre }}</option>
                @endforeach
            </select>

            <button class="btn btn-claro" type="submit">{{ __('messages.search') }}</button>

            @if (request('q') || request('categoria_id'))
                <a class="btn btn-claro" href="{{ route('admin.productos.index') }}">{{ __('messages.clear') }}</a>
            @endif
        </form>

        @if ($puedeEditar)
            <a class="btn btn-rosa" href="{{ route('admin.productos.create') }}">+ {{ __('messages.new_product') }}</a>
        @endif
    </div>

    <div class="card">
        @if ($productos->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.sale_price') }}</th>

                            @if ($rol !== 'Vendedor')
                                <th>{{ __('messages.purchase_price') }}</th>
                            @endif

                            <th>{{ __('messages.stock') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th style="text-align:right;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($productos as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->codigo }}</strong>

                                    @if ($p->codigo_barras)
                                        <span class="sub" title="{{ __('messages.barcode') }}">🏷️ {{ $p->codigo_barras }}</span>
                                    @endif
                                </td>

                                <td>
                                    <strong>{{ $p->nombre }}</strong>

                                    @if ($p->marca)
                                        <span class="sub">{{ __('messages.brand') }}: {{ $p->marca }}</span>
                                    @endif
                                </td>

                                <td>{{ $p->categoria_nombre ?? '—' }}</td>

                                <td>
                                    <strong>S/ {{ number_format($p->precio_venta, 2) }}</strong>
                                </td>

                                @if ($rol !== 'Vendedor')
                                    <td>S/ {{ number_format($p->precio_compra, 2) }}</td>
                                @endif

                                <td>
                                    @if ($p->stock_actual <= $p->stock_minimo)
                                        <span class="badge off" style="background:#fee2e2; color:#b91c1c;" title="{{ __('messages.low_stock_warning') }}">
                                            ⚠️ {{ number_format($p->stock_actual, 0) }} {{ $p->unidad_nombre ?? '' }}
                                        </span>
                                    @else
                                        <span class="badge ok">
                                            {{ number_format($p->stock_actual, 0) }} {{ $p->unidad_nombre ?? '' }}
                                        </span>
                                    @endif

                                    <span class="sub">{{ __('messages.minimum_short') }}: {{ number_format($p->stock_minimo, 0) }}</span>
                                </td>

                                <td>
                                    <span class="badge {{ $p->estado === 'activo' ? 'ok' : 'off' }}">
                                        {{ $p->estado === 'activo' ? __('messages.active') : __('messages.inactive') }}
                                    </span>
                                </td>

                                <td style="text-align:right;">
                                    <div class="acciones-td" style="justify-content: flex-end;">
                                        <a class="btn btn-claro btn-sm" href="{{ route('admin.productos.show', $p->id) }}">
                                            {{ __('messages.view') }}
                                        </a>

                                        @if ($puedeEditar)
                                            <a class="btn btn-claro btn-sm" href="{{ route('admin.productos.edit', $p->id) }}">
                                                {{ __('messages.edit') }}
                                            </a>
                                        @endif

                                        @if ($puedeEliminar && $p->estado === 'activo')
                                            <form method="POST" action="{{ route('admin.productos.destroy', $p->id) }}" onsubmit="return confirm('{{ __('messages.deactivate_product_confirmation') }}')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-rojo btn-sm" type="submit">
                                                    {{ __('messages.deactivate') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
                    <svg class="i" viewBox="0 0 24 24">
                        <path d="m7.5 4.27 9 5.15"/>
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                        <path d="m3.3 7 8.7 5 8.7-5"/>
                        <path d="M12 22V12"/>
                    </svg>
                </div>

                <h3>{{ __('messages.no_products_registered') }}</h3>

                <p>{{ __('messages.no_products_found') }}</p>

                @if ($puedeEditar)
                    <a class="btn btn-rosa" href="{{ route('admin.productos.create') }}">
                        + {{ __('messages.register_first_product') }}
                    </a>
                @endif
            </div>

        @endif
    </div>
@endsection
