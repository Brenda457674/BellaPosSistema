@extends('layouts.panel')

@section('titulo', __('messages.customers'))
@section('encabezado', __('messages.customers'))
@section('subtitulo', __('messages.customers_subtitle'))

@push('styles')
    @include('partials.ui')
@endpush

@section('contenido')
    @php
        $rol = auth()->user()->rol?->nombre ?? '';
        $puedeEliminar = ($rol === 'Administrador');
    @endphp

    <div class="barra">
        <form class="buscar" method="GET" action="{{ route('clientes.index') }}">
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, documento o teléfono...">
            <button class="btn btn-claro" type="submit">{{ __('messages.search') }}</button>
            @if ($q)
                <a class="btn btn-claro" href="{{ route('clientes.index') }}">{{ __('messages.clear') }}</a>
            @endif
        </form>
        <a class="btn btn-rosa" href="{{ route('clientes.create') }}">+ {{ __('messages.new_customer') }}</a>
    </div>

    <div class="card">
        @if ($clientes->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('messages.customer') }}</th>
                            <th>{{ __('messages.document') }}</th>
                            <th>{{ __('messages.phone') }} / {{ __('messages.email') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th style="text-align:right;">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->nombres }} {{ $c->apellidos }}</strong>
                                    @if ($c->direccion)
                                        <span class="sub">📍 {{ $c->direccion }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($c->numero_documento)
                                        <span class="badge ok" style="background:var(--fondo); color:var(--texto);">
                                            {{ $c->tipo_documento }}: {{ $c->numero_documento }}
                                        </span>
                                    @else
                                        <span class="sub">Sin documento</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($c->telefono)
                                        <div>📞 {{ $c->telefono }}</div>
                                    @endif
                                    @if ($c->email)
                                        <div class="sub">✉️ {{ $c->email }}</div>
                                    @endif
                                    @if (!$c->telefono && !$c->email)
                                        <span class="sub">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $c->estado === 'activo' ? 'ok' : 'off' }}">
                                        {{ ucfirst($c->estado) }}
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <div class="acciones-td" style="justify-content: flex-end;">
                                        <a class="btn btn-claro btn-sm" href="{{ route('clientes.edit', $c->id) }}">Editar</a>
                                        @if ($puedeEliminar)
                                            <form method="POST" action="{{ route('clientes.destroy', $c->id) }}" onsubmit="return confirm('¿Eliminar o desactivar a este cliente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-rojo btn-sm" type="submit">Eliminar</button>
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
                {{ $clientes->links() }}
            </div>
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg class="i" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3>No hay clientes registrados</h3>
                <p>No se encontraron clientes que coincidan con la búsqueda.</p>
                <a class="btn btn-rosa" href="{{ route('clientes.create') }}">+ Registrar cliente</a>
            </div>
        @endif
    </div>
@endsection
