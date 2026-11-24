@extends('layouts.panel')

@section('titulo', __('messages.dashboard'))

{{-- El dashboard usa su propia tarjeta de bienvenida, sin la cabecera normal --}}
@section('sin_cabecera', '1')

@push('styles')
    @include('partials.ui')
@endpush

@push('styles')
<style>
    /* ----- Tarjeta de bienvenida ----- */
    .hero {
        position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
        padding: 30px 32px; margin-bottom: 24px; border-radius: 22px; color: #fff;
        background:
            radial-gradient(420px 260px at 85% 0%, rgba(255, 211, 184, .55), transparent 65%),
            radial-gradient(380px 260px at 60% 120%, rgba(244, 166, 189, .5), transparent 65%),
            linear-gradient(135deg, #3b1230 0%, #7a2a52 55%, #b83f61 100%);
        box-shadow: 0 18px 40px rgba(59, 18, 48, .25);
    }
    .hero h1 { font-size: 28px; font-weight: 800; letter-spacing: -.8px; }
    .hero p { margin-top: 6px; font-size: 13.5px; color: rgba(255, 255, 255, .85); max-width: 460px; line-height: 1.5; }
    .hero-fecha { margin-top: 12px; display: inline-block; padding: 5px 12px; border-radius: 999px; background: rgba(255, 255, 255, .15); font-size: 12px; text-transform: capitalize; }
    .hero-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; position: relative; z-index: 1; }
    .hero-chip { padding: 10px 16px; border-radius: 14px; background: rgba(255, 255, 255, .14); backdrop-filter: blur(6px); text-align: right; }
    .hero-chip small { display: block; font-size: 11px; color: rgba(255, 255, 255, .75); }
    .hero-chip strong { font-size: 20px; font-weight: 800; }
    .hero-btn {
        padding: 13px 22px; border-radius: 999px; background: #fff; color: #3b1230;
        font-weight: 700; font-size: 13px; text-decoration: none;
        box-shadow: 0 10px 22px rgba(0, 0, 0, .2); transition: transform .15s;
    }
    .hero-btn:hover { transform: translateY(-2px); }

    /* ----- Cifras ----- */
    .kpi { border-radius: 18px; transition: transform .18s, box-shadow .18s; }
    .kpi:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(59, 18, 48, .1); }
    .kpi-icon { border-radius: 50%; }
    .kpi-value { font-weight: 800; color: #3b1230; }

    /* ----- Columna derecha ----- */
    .col-der { display: grid; gap: 16px; align-content: start; }
    .sub { margin-top: 2px; font-size: 11.5px; color: var(--texto-3); }

    /* ----- Actividad en vivo ----- */
    .vivo-estado { display: inline-flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 600; color: var(--texto-3); }
    .vivo-punto { width: 9px; height: 9px; border-radius: 50%; background: #c9bcc2; }
    .vivo-estado.on { color: var(--verde); }
    .vivo-estado.on .vivo-punto { background: #2f855a; box-shadow: 0 0 0 0 rgba(47, 133, 90, .6); animation: pulso 1.8s infinite; }
    @keyframes pulso { 70% { box-shadow: 0 0 0 8px rgba(47, 133, 90, 0); } 100% { box-shadow: 0 0 0 0 rgba(47, 133, 90, 0); } }
    .feed { max-height: 300px; overflow-y: auto; }
    .feed-vacio { padding: 30px 20px; text-align: center; color: var(--texto-3); font-size: 12.5px; line-height: 1.5; }
    .feed-item { display: flex; gap: 12px; align-items: flex-start; padding: 13px 20px; border-bottom: 1px solid var(--borde); animation: entra .35s ease; }
    .feed-item:last-child { border-bottom: 0; }
    .feed-ic { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0; font-size: 15px; background: var(--verde-bg); }
    .feed-item.stock .feed-ic { background: #fee2e2; }
    .feed-item strong { font-size: 13px; display: block; }
    .feed-item small { color: var(--texto-3); font-size: 11.5px; }
    @keyframes entra { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
</style>
@endpush

@section('contenido')

    {{-- BIENVENIDA --}}
    <section class="hero">
        <div>
            <h1>{{ __('messages.hello') }}, {{ $usuario->nombres ?? __('messages.administrator') }}</h1>
            <p>{{ __('messages.dashboard_subtitle') }}</p>
            <span class="hero-fecha">{{ now()->locale(app()->getLocale())->translatedFormat('l, d \d\e F \d\e Y') }}</span>
        </div>

        <div class="hero-right">
            <div class="hero-chip">
                <small>{{ __('messages.today_sales') }}</small>
                <strong>S/ {{ number_format((float) $montoVentasDelDia, 2) }}</strong>
            </div>
            <a class="hero-btn" href="{{ route('pos.create') }}">+ {{ __('messages.new_sale') }}</a>
        </div>
    </section>

    {{-- TARJETAS PRINCIPALES --}}
    <div class="kpis">

        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">{{ __('messages.today_sales') }}</span>
                <span class="kpi-icon rosa">💵</span>
            </div>
            <div class="kpi-value">S/ {{ number_format((float) $montoVentasDelDia, 2) }}</div>
            <div class="kpi-note">
                {{ $ventasDelDia }}
                {{ (int) $ventasDelDia === 1 ? __('messages.transaction') : __('messages.transactions') }}
                {{ __('messages.today') }}
            </div>
        </article>

        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">{{ __('messages.active_products') }}</span>
                <span class="kpi-icon azul">📦</span>
            </div>
            <div class="kpi-value">{{ $totalProductos }}</div>
            <div class="kpi-note">{{ __('messages.available_catalog') }}</div>
        </article>

        <article class="kpi" style="{{ $productosStockBajo > 0 ? 'border-color:#fca5a5;' : '' }}">
            <div class="kpi-top">
                <span class="kpi-label" style="{{ $productosStockBajo > 0 ? 'color:#b91c1c;font-weight:700;' : '' }}">
                    {{ __('messages.low_stock') }}
                </span>
                <span class="kpi-icon ambar" style="{{ $productosStockBajo > 0 ? 'background:#fee2e2;color:#b91c1c;' : '' }}">⚠️</span>
            </div>
            <div class="kpi-value" style="{{ $productosStockBajo > 0 ? 'color:#b91c1c;' : '' }}" id="kpiStockBajo">
                {{ $productosStockBajo }}
            </div>
            <div class="kpi-note">{{ __('messages.below_minimum_stock') }}</div>
        </article>

        <article class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">{{ __('messages.active_users') }}</span>
                <span class="kpi-icon verde">👥</span>
            </div>
            <div class="kpi-value">{{ $usuariosActivos }}</div>
            <div class="kpi-note">{{ __('messages.accounts_with_access') }}</div>
        </article>

    </div>

    {{-- ACTIVIDAD --}}
    <div class="grid-2">

        {{-- VENTAS RECIENTES --}}
        <section class="card">
            <div class="card-head">
                <h2>{{ __('messages.recent_sales') }}</h2>
                <a class="btn btn-claro btn-sm" href="{{ route('pos.index') }}">
                    {{ __('messages.view_all') }}
                </a>
            </div>

            @if ($ventasRecientes->count())

                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('messages.receipt') }}</th>
                                <th>{{ __('messages.customer') }}</th>
                                <th style="text-align:right;">{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ventasRecientes as $vr)
                                <tr>
                                    <td>
                                        <a href="{{ route('pos.show', $vr->id) }}"
                                           style="font-weight:700;color:var(--texto);text-decoration:none;">
                                            {{ $vr->serie }}-{{ $vr->numero }}
                                        </a>

                                        <div class="sub">
                                            {{ \Carbon\Carbon::parse($vr->fecha_venta)->locale(app()->getLocale())->diffForHumans() }}
                                        </div>
                                    </td>

                                    <td>{{ $vr->cliente_nombre }}</td>

                                    <td style="text-align:right;">
                                        <strong>S/ {{ number_format($vr->total, 2) }}</strong>
                                    </td>

                                    <td>
                                        <span class="badge {{ $vr->estado === 'pagada' ? 'ok' : 'off' }}"
                                              style="{{ $vr->estado === 'pagada' ? 'background:#e8f5ee;color:#2f855a;' : '' }}{{ $vr->estado === 'anulada' ? 'background:#fee2e2;color:#b91c1c;' : '' }}">
                                            @switch($vr->estado)
                                                @case('pagada')
                                                    {{ __('messages.paid') }}
                                                    @break
                                                @case('anulada')
                                                    {{ __('messages.cancelled') }}
                                                    @break
                                                @default
                                                    {{ ucfirst($vr->estado) }}
                                            @endswitch
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else

                <div class="empty">
                    <div class="empty-icon">🧾</div>
                    <h3>{{ __('messages.no_sales_yet') }}</h3>
                    <p>{{ __('messages.no_sales_description') }}</p>

                    <a class="btn btn-rosa" href="{{ route('pos.create') }}">
                        + {{ __('messages.new_sale') }}
                    </a>
                </div>

            @endif
        </section>

        {{-- COLUMNA DERECHA: actividad en vivo + más vendidos --}}
        <div class="col-der">

            {{-- ACTIVIDAD EN VIVO (aquí llegarán los avisos de Reverb) --}}
            <section class="card">
                <div class="card-head">
                    <h2>Actividad en vivo</h2>
                    <span class="vivo-estado" id="vivoEstado">
                        <span class="vivo-punto"></span>
                        <span id="vivoTexto">Sin conexión</span>
                    </span>
                </div>
                <div class="feed" id="feedVivo">
                    <div class="feed-vacio" id="feedVacio">
                        Aquí aparecerán las ventas y los avisos de stock bajo en tiempo real.
                    </div>
                </div>
            </section>

            {{-- PRODUCTOS MÁS VENDIDOS --}}
            <section class="card">
                <div class="card-head">
                    <h2>{{ __('messages.best_selling_products') }}</h2>

                    <a class="btn btn-claro btn-sm" href="{{ route('reportes.index') }}">
                        {{ __('messages.view_report') }}
                    </a>
                </div>

                @if ($topProductos->count())

                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>{{ __('messages.product') }}</th>
                                    <th style="text-align:right;">{{ __('messages.sold') }}</th>
                                    <th style="text-align:right;">{{ __('messages.stock') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($topProductos as $tp)
                                    <tr>
                                        <td>
                                            <strong>{{ $tp->nombre }}</strong>
                                            <div class="sub">[{{ $tp->codigo }}]</div>
                                        </td>

                                        <td style="text-align:right;">
                                            <strong style="color:var(--rosa-oscuro);">
                                                {{ number_format($tp->total_cantidad, 0) }}
                                            </strong>
                                        </td>

                                        <td style="text-align:right;">
                                            {{ number_format($tp->stock_actual, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else

                    <div class="empty">
                        <div class="empty-icon">📦</div>
                        <h3>{{ __('messages.no_sales_data') }}</h3>
                        <p>{{ __('messages.best_products_description') }}</p>
                    </div>

                @endif
            </section>

        </div>
    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <h2 class="section-title">{{ __('messages.admin_shortcuts') }}</h2>

    <div class="actions">

        <a class="action" href="{{ route('pos.create') }}">
            <span class="ic">💳</span>
            <span>
                {{ __('messages.new_sale') }}
                <small>{{ __('messages.pos_description') }}</small>
            </span>
        </a>

        <a class="action" href="{{ route('compras.create') }}">
            <span class="ic">🛒</span>
            <span>
                {{ __('messages.register_purchase') }}
                <small>{{ __('messages.purchase_description') }}</small>
            </span>
        </a>

        <a class="action" href="{{ route('admin.productos.create') }}">
            <span class="ic">📦</span>
            <span>
                {{ __('messages.new_product') }}
                <small>{{ __('messages.add_to_catalog') }}</small>
            </span>
        </a>

        <a class="action" href="{{ route('inventario.ajuste') }}">
            <span class="ic">⚙️</span>
            <span>
                {{ __('messages.adjust_stock') }}
                <small>{{ __('messages.warehouse_correction') }}</small>
            </span>
        </a>

        <a class="action" href="{{ route('reportes.index') }}">
            <span class="ic">📊</span>
            <span>
                {{ __('messages.view_reports') }}
                <small>{{ __('messages.metrics_revenue') }}</small>
            </span>
        </a>

        <a class="action" href="{{ route('usuarios.index') }}">
            <span class="ic">👥</span>
            <span>
                {{ __('messages.manage_users') }}
                <small>{{ __('messages.roles_accounts') }}</small>
            </span>
        </a>

    </div>

@endsection
