
@extends('layouts.panel')

@section('titulo', __('messages.product_detail') . ': ' . $producto->nombre)
@section('encabezado', $producto->nombre)
@section('subtitulo', __('messages.code') . ': ' . $producto->codigo . ($producto->codigo_barras ? ' · ' . __('messages.barcode') . ': ' . $producto->codigo_barras : ''))

@push('styles')
    @include('partials.ui')
    <style>
        .ficha-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 20px; }
        .dato-fila { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--borde); font-size: 13.5px; }
        .dato-label { color: var(--texto-2); font-weight: 500; }
        .dato-valor { font-weight: 600; text-align: right; }
        @media (max-width: 800px) { .ficha-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('contenido')
    @php
        $rol = auth()->user()->rol?->nombre ?? '';
        $puedeEditar = in_array($rol, ['Administrador', 'Almacenero'], true);
        $puedeEliminar = ($rol === 'Administrador');
    @endphp

    <div class="ficha-grid">

        <div class="card">
            <div class="card-head">
                <h2>{{ __('messages.general_information') }}</h2>
                <span class="badge {{ $producto->estado === 'activo' ? 'ok' : 'off' }}">
                    {{ $producto->estado === 'activo' ? __('messages.active') : __('messages.inactive') }}
                </span>
            </div>

            <div class="card-body">
                <div class="dato-fila">
                    <span class="dato-label">{{ __('messages.internal_code') }}</span>
                    <span class="dato-valor">{{ $producto->codigo }}</span>
                </div>

                <div class="dato-fila">
                    <span class="dato-label">{{ __('messages.barcode') }}</span>
                    <span class="dato-valor">{{ $producto->codigo_barras ?: '—' }}</span>
                </div>

                <div class="dato-fila">
                    <span class="dato-label">{{ __('messages.category') }}</span>
                    <span class="dato-valor">{{ $producto->categoria_nombre ?? __('messages.no_category') }}</span>
                </div>

                <div class="dato-fila">
                    <span class="dato-label">{{ __('messages.unit_of_measure') }}</span>
                    <span class="dato-valor">{{ $producto->unidad_nombre ?? __('messages.unit') }}</span>
                </div>

                <div class="dato-fila">
                    <span class="dato-label">{{ __('messages.brand') }}</span>
                    <span class="dato-valor">{{ $producto->marca ?: __('messages.no_brand') }}</span>
                </div>

                <div class="dato-fila">
                    <span class="dato-label">{{ __('messages.expiration_control') }}</span>
                    <span class="dato-valor">
                        @if ($producto->controla_vencimiento)
                            <span class="badge ok">{{ __('messages.yes') }}</span>
                        @else
                            <span class="badge off">{{ __('messages.no') }}</span>
                        @endif
                    </span>
                </div>

                <div class="dato-fila" style="border-bottom:none;">
                    <span class="dato-label">{{ __('messages.description') }}</span>
                    <span class="dato-valor" style="max-width: 60%;">
                        {{ $producto->descripcion ?: __('messages.no_description') }}
                    </span>
                </div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:20px;">

            <div class="card">
                <div class="card-head">
                    <h2>{{ __('messages.prices_inventory') }}</h2>
                </div>

                <div class="card-body">
                    <div class="dato-fila">
                        <span class="dato-label">{{ __('messages.public_sale_price') }}</span>
                        <span class="dato-valor" style="color:var(--rosa-oscuro); font-size:16px;">
                            S/ {{ number_format($producto->precio_venta, 2) }}
                        </span>
                    </div>

                    @if ($rol !== 'Vendedor')
                        <div class="dato-fila">
                            <span class="dato-label">{{ __('messages.purchase_price_cost') }}</span>
                            <span class="dato-valor">
                                S/ {{ number_format($producto->precio_compra, 2) }}
                            </span>
                        </div>

                        @php
                            $ganancia = $producto->precio_venta - $producto->precio_compra;
                            $margenPct = $producto->precio_compra > 0 ? ($ganancia / $producto->precio_compra) * 100 : 0;
                        @endphp

                        <div class="dato-fila">
                            <span class="dato-label">{{ __('messages.estimated_gross_margin') }}</span>
                            <span class="dato-valor" style="color:var(--verde);">
                                S/ {{ number_format($ganancia, 2) }} (+{{ number_format($margenPct, 1) }}%)
                            </span>
                        </div>
                    @endif

                    <div class="dato-fila">
                        <span class="dato-label">{{ __('messages.current_store_stock') }}</span>
                        <span class="dato-valor">
                            @if ($producto->stock_actual <= $producto->stock_minimo)
                                <span class="badge off" style="background:#fee2e2; color:#b91c1c;">
                                    ⚠️ {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_nombre ?? '' }}
                                </span>
                            @else
                                <span class="badge ok">
                                    {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_nombre ?? '' }}
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="dato-fila" style="border-bottom:none;">
                        <span class="dato-label">{{ __('messages.configured_minimum_stock') }}</span>
                        <span class="dato-valor">
                            {{ number_format($producto->stock_minimo, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h2>{{ __('messages.actions') }}</h2>
                </div>

                <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">

                    <a class="btn btn-claro" href="{{ route('admin.productos.index') }}">
                        ← {{ __('messages.back_to_products') }}
                    </a>

                    @if ($puedeEditar)
                        <a class="btn btn-rosa" href="{{ route('admin.productos.edit', $producto->id) }}">
                            ✏️ {{ __('messages.edit_this_product') }}
                        </a>
                    @endif

                    @if ($puedeEliminar && $producto->estado === 'activo')
                        <form method="POST"
                              action="{{ route('admin.productos.destroy', $producto->id) }}"
                              onsubmit="return confirm('{{ __('messages.confirm_deactivate_product') }}')"
                              style="margin:0;">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-rojo" type="submit" style="width:100%;">
                                🚫 {{ __('messages.deactivate_product') }}
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
