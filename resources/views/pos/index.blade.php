@extends('layouts.panel')

@section('titulo', $esAdmin ? __('messages.sales') : __('messages.my_sales'))
@section('encabezado', $esAdmin ? __('messages.sales_history') : __('messages.my_sales'))
@section('subtitulo', $esAdmin ? __('messages.sales_history_sub') : __('messages.my_sales_sub'))

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('pos.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('messages.search_sale_placeholder') }}">

            <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">

            <select name="estado" onchange="this.form.submit()" style="padding:10px 12px; border:1px solid var(--borde); border-radius:9px; background:#fff; font:inherit;">
                <option value="">{{ __('messages.all_statuses') }}</option>
                <option value="pagada" @selected($estado === 'pagada')>{{ __('messages.paid') }}</option>
                <option value="anulada" @selected($estado === 'anulada')>{{ __('messages.cancelled') }}</option>
            </select>

            <button class="btn btn-claro" type="submit">{{ __('messages.search') }}</button>
            @if ($q || $fecha || $estado)
                <a class="btn btn-claro" href="{{ route('pos.index') }}">{{ __('messages.clear') }}</a>
            @endif
        </form>

        <a class="btn btn-rosa" href="{{ route('pos.create') }}">+ {{ __('messages.new_sale') }}</a>
    </div>

    <div class="card">
        @if ($ventas->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('messages.receipt') }}</th>
                            <th>{{ __('messages.date_time') }}</th>
                            <th>{{ __('messages.customer') }}</th>
                            @if ($esAdmin)
                                <th>{{ __('messages.seller') }}</th>
                            @endif
                            <th>{{ __('messages.total') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th style="text-align:right;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $v)
                            <tr>
                                <td>
                                    <strong>{{ __('messages.ticket') }} {{ $v->serie }}-{{ $v->numero }}</strong>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($v->fecha_venta)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    <strong>{{ $v->cliente_nombre }}</strong>
                                    @if ($v->cliente_doc)
                                        <span class="sub">Doc: {{ $v->cliente_doc }}</span>
                                    @endif
                                </td>
                                @if ($esAdmin)
                                    <td>{{ $v->vendedor_nombre }}</td>
                                @endif
                                <td>
                                    <strong style="color:var(--texto); font-size:14.5px;">S/ {{ number_format($v->total, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $v->estado === 'pagada' ? 'ok' : 'off' }}" style="{{ $v->estado === 'anulada' ? 'background:#fee2e2; color:#b91c1c;' : '' }}">
                                        {{ $v->estado === 'pagada' ? __('messages.paid') : __('messages.cancelled') }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <a class="btn btn-claro btn-sm" href="{{ route('pos.show', $v->id) }}">{{ __('messages.view_ticket') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="paginacion">
                {{ $ventas->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 17.5v-11"/></svg>
                </div>
                <h3>{{ __('messages.no_sales_yet') }}</h3>
                <p>{{ __('messages.no_sales_description') }}</p>
                <a class="btn btn-rosa" href="{{ route('pos.create') }}">+ {{ __('messages.new_sale') }}</a>
            </div>
        @endif
    </div>
@endsection
